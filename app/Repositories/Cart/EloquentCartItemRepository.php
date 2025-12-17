<?php

namespace App\Repositories\Cart;

use App\Exceptions\DomainException;
use App\Models\CartItem;

class EloquentCartItemRepository implements CartItemRepositoryInterface
{
    public function findByCartAndVariant(int $cartId, int $variantId): ?CartItem
    {
        return CartItem::query()
            ->where('cart_id', $cartId)
            ->where('product_variant_id', $variantId)
            ->first();
    }

    public function upsertItem(int $cartId, int $variantId, int $qty, string $unitPrice): CartItem
    {
        // Öneri: cart_items üzerinde (cart_id, product_variant_id) unique index olmalı.
        // Bu sayede updateOrCreate yarış durumunda daha güvenli olur.

        $item = CartItem::query()->firstOrNew([
            'cart_id' => $cartId,
            'product_variant_id' => $variantId,
        ]);

        $currentQty = (int) ($item->quantity ?? 0);
        $item->quantity = $currentQty + $qty;
        $item->unit_price = $unitPrice;

        $item->save();

        return $item->fresh();
    }

    public function updateQuantityByCartVariant(int $cartId, int $variantId, int $qty): CartItem
    {
        $item = $this->findByCartAndVariant($cartId, $variantId);

        if (!$item) {
            throw new DomainException('Cart item not found', 404);
        }

        $item->quantity = $qty;
        $item->save();

        return $item->fresh();
    }

    public function delete(CartItem $item): void
    {
        $item->delete();
    }
}