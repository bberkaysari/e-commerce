<?php

namespace App\Services\Order;

use App\Models\Order;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface OrderServiceInterface
{
    public function getOrders(int $userId, int $perPage = 15): LengthAwarePaginator;

    public function getOrder(int $orderId, int $userId): ?Order;
}
