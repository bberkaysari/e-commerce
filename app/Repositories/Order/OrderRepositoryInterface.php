<?php

namespace App\Repositories\Order;

use App\Models\Order;

interface OrderRepositoryInterface
{
    public function create(array $data): Order;

    public function findById(int $id): ?Order;

    public function findByIdWithDetails(int $id): ?Order;

    public function updateStatus(Order $order, string $status): Order;

    public function paginate(int $userId, int $perPage = 15);

    public function updateTotals(Order $order): Order;
}