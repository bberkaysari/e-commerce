<?php

namespace App\Repositories\Order;

use App\Models\OrderAddress;
use App\Repositories\Interfaces\OrderAddressRepositoryInterface;

class OrderAddressRepository implements OrderAddressRepositoryInterface
{
    public function bulkCreate(array $rows): void
    {
        OrderAddress::query()->insert($rows);
    }
}
