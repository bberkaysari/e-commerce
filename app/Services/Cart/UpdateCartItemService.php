<?php

namespace App\Services\Cart;

use App\Exceptions\DomainException;
use App\Exceptions\VariantNotFoundException;
use App\Repositories\Cart\CartItemRepositoryInterface;
use App\Repositories\Cart\CartRepositoryInterface;
use App\Repositories\Variant\VariantRepositoryInterface;
use Illuminate\Support\Facades\Log;

class UpdateCartItemService
{
    public function __construct(
        private readonly CartRepositoryInterface $cartRepository,
        private readonly CartItemRepositoryInterface $cartItemRepository,
        private readonly VariantRepositoryInterface $variantRepository,
    ) {}

    public function execute(array $data)
    {
        $userId = (int) $data['user_id'];
        $variantId = (int) $data['variant_id'];
        $qty = (int) $data['quantity'];

        try {
            $variant = $this->variantRepository->findById($variantId);
            if (!$variant) {
                throw new VariantNotFoundException($variantId);
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