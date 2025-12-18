<?php

namespace App\Services\Cart;

use App\Exceptions\DomainException;
use App\Exceptions\InactiveVariantException;
use App\Exceptions\VariantNotFoundException;
use App\Models\Cart;
use App\Repositories\Interfaces\CartItemRepositoryInterface;
use App\Repositories\Interfaces\CartRepositoryInterface;
use App\Repositories\Interfaces\VariantRepositoryInterface;
use App\Services\Interfaces\UpdateCartItemServiceInterface;
use Illuminate\Support\Facades\Log;

class UpdateCartItemService implements UpdateCartItemServiceInterface
{
    public function __construct(
        private readonly CartRepositoryInterface $cartRepository,
        private readonly CartItemRepositoryInterface $cartItemRepository,
        private readonly VariantRepositoryInterface $variantRepository,
    ) {}

    public function execute(array $data): ?Cart
    {
        $userId = (int) $data['user_id'];
        $variantId = (int) $data['variant_id'];
        $qty = (int) $data['quantity'];

        // Quantity validation
        if ($qty <= 0) {
            throw new DomainException('Quantity must be greater than zero', 422);
        }

        try {
            $variant = $this->variantRepository->findById($variantId);
            if (!$variant) {
                throw new VariantNotFoundException($variantId);
            }

            // Variant aktif mi kontrol et
            if (!(bool) $variant->status) {
                throw new InactiveVariantException($variantId);
            }

            // Stok kontrolü
            $available = (int) $variant->stock_quantity;
            if ($available < $qty) {
                throw new DomainException(
                    "Insufficient stock for variant {$variantId}. Requested: {$qty}, Available: {$available}",
                    422
                );
            }

            $cart = $this->cartRepository->getOrCreateActiveCart($userId);

            $this->cartItemRepository->updateQuantityByCartVariant($cart->id, $variantId, $qty);

            return $this->cartRepository->getActiveCartWithItems($userId);
        } catch (DomainException $e) {
            throw $e;
        } catch (\Throwable $e) {
            Log::error('UpdateCartItem failed', [
                'error' => $e->getMessage(),
                'user_id' => $userId,
                'variant_id' => $variantId,
                'quantity' => $qty,
            ]);

            throw new DomainException('Unexpected error while updating cart item', 500);
        }
    }
}
