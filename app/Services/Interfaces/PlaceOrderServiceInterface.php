<?php

namespace App\Services\Interfaces;

use App\Models\Order;

interface PlaceOrderServiceInterface
{
    public function execute(array $data): Order;
}