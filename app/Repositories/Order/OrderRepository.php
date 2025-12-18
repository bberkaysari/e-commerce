<?php

namespace App\Repositories\Order;

use App\Models\Order;
use App\Repositories\Interfaces\OrderRepositoryInterface;

class OrderRepository implements OrderRepositoryInterface
{
    public function create(array $data): Order
    {
        return Order::query()->create($data);
    }

    public function findById(int $id): ?Order
    {
        return Order::query()->find($id);
    }

    public function findByIdWithDetails(int $id): ?Order
    {
        return Order::query()
            ->with([
                'items',
                'addresses',
                'items.variant:id,product_id,sku,price,stock_quantity,attributes,status',
                'items.variant.product:id,name,status',
            ])
            ->find($id);
    }

    public function updateStatus(Order $order, string $status): Order
    {
        $order->status = $status;
        $order->save();

        return $order->fresh();
    }

    public function paginate(int $userId, int $perPage = 15)
    {
        return Order::query()
            ->where('user_id', $userId)
            ->with(['items', 'addresses'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }
}
