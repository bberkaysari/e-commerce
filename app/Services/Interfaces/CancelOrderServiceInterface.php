<?php

namespace App\Services\Interfaces;

use App\Models\Order;

interface CancelOrderServiceInterface
{
    public function execute(int $orderId, int $userId): Order;
}