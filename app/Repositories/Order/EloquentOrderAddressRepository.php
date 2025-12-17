<?php

namespace App\Repositories\Order;

use App\Models\OrderAddress;

class EloquentOrderAddressRepository implements OrderAddressRepositoryInterface
{
    public function bulkCreate(array $rows): void
    {
        OrderAddress::query()->insert($rows);
    }
}