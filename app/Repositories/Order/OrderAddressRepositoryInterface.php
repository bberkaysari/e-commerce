<?php

namespace App\Repositories\Order;

use App\Models\OrderAddress;

interface OrderAddressRepositoryInterface
{
    public function bulkCreate(array $rows): void;
    
}