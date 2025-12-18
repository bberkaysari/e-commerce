<?php

namespace App\Services\Cart;
use App\Services\Interfaces\AddToCartServiceInterface;

use App\Exceptions\DomainException;
use App\Exceptions\InactiveVariantException;
use App\Exceptions\VariantNotFoundException;
use App\Models\Cart;
use App\Repositories\Interfaces\CartItemRepositoryInterface;
use App\Repositories\Interfaces\CartRepositoryInterface;
use App\Repositories\Interfaces\VariantRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AddToCartService implements AddToCartServiceInterface
{
    public function __construct(
        private readonly CartRepositoryInterface $cartRepository,
        private readonly CartItemRepositoryInterface $cartItemRepository,
        private readonly VariantRepositoryInterface $variantRepository,
    ) {}

    public function execute(array $data): Cart
    {
        $userId = (int) $data['user_id'];
        $variantId = (int) $data['variant_id'];
        $qty = (int) $data['quantity'];

        if ($qty <= 0) {
            throw new DomainException('Quantity must be greater than zero', 422);
        }

        try {
            return DB::transaction(function () use ($userId, $variantId, $qty) {
                $variant = $this->variantRepository->findById($variantId);

                if (!$variant) {
                    throw new VariantNotFoundException($variantId);
                }

                if (!(bool) $variant->status) {
                    throw new InactiveVariantException($variantId);
                }

                // Stok kontrolü: Sepete eklerken de kontrol et
                $available = (int) $variant->stock_quantity;
                if ($available < $qty) {
                    throw new DomainException(
                        "Insufficient stock for variant {$variantId}. Requested: {$qty}, Available: {$available}",
                        422
                    );
                }

                $cart = $this->cartRepository->getOrCreateActiveCart($userId);

                // price snapshot
                $unitPrice = (string) $variant->price;

                $this->cartItemRepository->upsertItem(
                    $cart->id,
                    $variantId,
                    $qty,
                    $unitPrice
                );

                return $this->cartRepository->getActiveCartWithItems($userId);
            }, 3);
        } catch (DomainException $e) {
            throw $e;
        } catch (\Throwable $e) {
            Log::error('AddToCart failed', [
                'error' => $e->getMessage(),
                'user_id' => $userId,
                'variant_id' => $variantId,
                'quantity' => $qty,
            ]);

            throw new DomainException('Unexpected error while adding item to cart', 500);
        }
    }
}
