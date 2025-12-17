<?php

namespace App\Repositories\Payment;

use App\Models\Payment;

class EloquentPaymentRepository implements PaymentRepositoryInterface
{
    public function create(array $data): Payment
    {
        return Payment::create($data);
    }

    public function findById(int $id): ?Payment
    {
        return Payment::find($id);
    }

    public function findByOrderId(int $orderId): ?Payment
    {
        return Payment::where('order_id', $orderId)->first();
    }
}
