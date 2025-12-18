<?php

namespace App\Services\Interfaces;

interface PaymentServiceInterface
{
    /**
     * @param array{
     *     user_id: int,
     *     order_id: int,
     *     payment_method?: string|null,
     *     payment_details?: mixed
     * } $data
     */
    public function execute(array $data): array;
}