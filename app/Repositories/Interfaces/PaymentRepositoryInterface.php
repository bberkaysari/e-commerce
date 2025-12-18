<?php

namespace App\Repositories\Interfaces;

use App\Models\Payment;

interface PaymentRepositoryInterface
{
    public function create(array $data): Payment;

    public function findById(int $id): ?Payment;

    public function findByOrderId(int $orderId): ?Payment;
}
