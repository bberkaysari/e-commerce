<?php

namespace App\Repositories\Cart;

use App\Models\CartItem;

interface CartItemRepositoryInterface
{
    public function upsertItem(int $cartId, int $variantId, int $qty, string $unitPrice): CartItem;

    public function updateQuantityByCartVariant(int $cartId, int $variantId, int $qty): CartItem;

    public function findByCartAndVariant(int $cartId, int $variantId): ?CartItem;

    public function delete(CartItem $item): void;
}