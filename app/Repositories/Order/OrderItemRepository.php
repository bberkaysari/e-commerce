<?php

namespace App\Repositories\Order;

use App\Models\OrderItem;
use App\Repositories\Interfaces\OrderItemRepositoryInterface;

class OrderItemRepository implements OrderItemRepositoryInterface
{
    public function bulkCreate(array $rows): void
    {
        OrderItem::query()->insert($rows);
    }
}
