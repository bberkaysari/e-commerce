<?php

namespace App\Repositories\Order;

use App\Models\OrderItem;

interface OrderItemRepositoryInterface
{
    public function bulkCreate(array $rows): void;
}