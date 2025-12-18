<?php

namespace App\Services\Interfaces;

use App\Models\Order;

interface RefundOrderServiceInterface
{
    public function execute(int $orderId, int $userId): Order;
}