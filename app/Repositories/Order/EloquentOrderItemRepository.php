<?php

namespace App\Repositories\Order;

use App\Models\OrderItem;

class EloquentOrderItemRepository implements OrderItemRepositoryInterface
{
    public function bulkCreate(array $rows): void
    {
        OrderItem::query()->insert($rows);
    }
}