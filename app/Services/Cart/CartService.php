<?php

namespace App\Services\Cart;

use App\Repositories\Cart\CartItemRepositoryInterface;
use App\Repositories\Cart\CartRepositoryInterface;

class CartService
{
    public function __construct(
        private readonly CartRepositoryInterface $cartRepository,
        private readonly CartItemRepositoryInterface $cartItemRepository
    ) {
    }

    public function getCart(int $userId): mixed
    {
        return $this->cartRepository->getActiveCartWithItems($userId);
    }

    public function updateCartItem(int $userId, int $variantId, int $quantity): mixed
    {
        $cart = $this->cartRepository->getOrCreateActiveCart($userId);

        $item = $this->cartItemRepository->findByCartAndVariant($cart->id, $variantId);

        if (!$item) {
            return null;
        }

        $this->cartItemRepository->updateQuantity($item, $quantity);

        return $this->cartRepository->getActiveCartWithItems($userId);
    }

    public function removeCartItem(int $userId, int $variantId): mixed
    {
        $cart = $this->cartRepository->getOrCreateActiveCart($userId);

        $item = $this->cartItemRepository->findByCartAndVariant($cart->id, $variantId);

        if (!$item) {
            return null;
        }

        $this->cartItemRepository->delete($item);

        return $this->cartRepository->getActiveCartWithItems($userId);
    }
}
