<?php

namespace App\Services\Cart;

use App\Models\Cart;
use App\Repositories\Interfaces\CartRepositoryInterface;
use App\Services\Interfaces\CartServiceInterface;

class CartService implements CartServiceInterface
{
    public function __construct(
        private readonly CartRepositoryInterface $cartRepository
    ) {
    }

    public function getActiveCart(int $userId): ?Cart
    {
        return $this->cartRepository->getActiveCartWithItems($userId);
    }
}
