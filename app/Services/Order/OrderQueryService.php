<?php

namespace App\Services\Order;
use App\Services\Interfaces\OrderQueryServiceInterface;

use App\Exceptions\DomainException;
use App\Models\Order;
use App\Repositories\Interfaces\OrderRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Symfony\Component\HttpFoundation\Response;

class OrderQueryService implements OrderQueryServiceInterface
{
    public function __construct(
        private readonly OrderRepositoryInterface $orderRepository
    ) {
    }

    public function paginateForUser(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->orderRepository->paginate($userId, $perPage);
    }

    public function findOwnedOrder(int $orderId, int $userId): Order
    {
        $order = $this->orderRepository->findByIdWithDetails($orderId);

        if (!$order) {
            throw new DomainException('Order bulunamadi', Response::HTTP_NOT_FOUND);
        }

        if ((int) $order->user_id !== $userId) {
            throw new DomainException('Unauthorized', Response::HTTP_FORBIDDEN);
        }

        return $order;
    }
}
