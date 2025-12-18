<?php

namespace App\Services\Interfaces;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Models\Order;

interface OrderQueryServiceInterface
{
    public function paginateForUser(int $userId, int $perPage = 15): LengthAwarePaginator;

    public function findOwnedOrder(int $orderId, int $userId): Order;
}