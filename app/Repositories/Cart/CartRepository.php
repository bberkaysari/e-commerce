<?php

namespace App\Repositories\Cart;

use App\Models\Cart;
use App\Repositories\Interfaces\CartRepositoryInterface;

class CartRepository implements CartRepositoryInterface
{
    public function getOrCreateActiveCart(int $userId): Cart
    {
        return Cart::query()->firstOrCreate([
            'user_id' => $userId,
            'status' => 'active',
        ]);
    }

    public function getActiveCartWithItems(int $userId): ?Cart
    {
        return Cart::query()
            ->select(['id', 'user_id', 'status', 'created_at', 'updated_at'])
            ->where('user_id', $userId)
            ->where('status', 'active')
            ->with([
                'items:id,cart_id,product_variant_id,quantity,unit_price,created_at,updated_at',
                'items.variant:id,product_id,sku,price,stock_quantity,attributes,status',
                'items.variant.product:id,name,status',
            ])
            ->first();
    }

    public function markCheckedOut(Cart $cart): Cart
    {
        $cart->status = 'checked_out';
        $cart->save();

        return $cart->fresh();
    }
}
