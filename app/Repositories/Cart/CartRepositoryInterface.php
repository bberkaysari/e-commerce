<?php

namespace App\Repositories\Cart;

use App\Models\Cart;

interface CartRepositoryInterface
{
    public function getOrCreateActiveCart(int $userId): Cart;

    public function getActiveCartWithItems(int $userId): ?Cart;

    public function markCheckedOut(Cart $cart): Cart;
}