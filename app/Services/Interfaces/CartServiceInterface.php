<?php

namespace App\Services\Interfaces;

use App\Models\Cart;

interface CartServiceInterface
{
    public function getActiveCart(int $userId): ?Cart;
}
