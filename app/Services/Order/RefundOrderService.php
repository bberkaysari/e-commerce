<?php

namespace App\Services\Order;
use App\Services\Interfaces\RefundOrderServiceInterface;

use App\Exceptions\DomainException;
use App\Models\Order;
use App\Repositories\Interfaces\OrderRepositoryInterface;
use App\Repositories\Interfaces\VariantRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class RefundOrderService implements RefundOrderServiceInterface
{
    public function __construct(
        private readonly OrderRepositoryInterface $orderRepository,
        private readonly VariantRepositoryInterface $variantRepository
    ) {}

    public function execute(int $orderId, int $userId): Order
    {
        try {
            return DB::transaction(function () use ($orderId, $userId) {
                $order = $this->orderRepository->findByIdWithDetails($orderId);

                if (!$order) {
                    throw new DomainException('Order not found', Response::HTTP_NOT_FOUND);
                }

                if ((int) $order->user_id !== $userId) {
                    throw new DomainException('Unauthorized', Response::HTTP_FORBIDDEN);
                }

                if (!in_array($order->status, ['PAID', 'SHIPPED', 'DELIVERED'], true)) {
                    throw new DomainException(
                        'Order cannot be refunded. Current status: ' . $order->status,
                        Response::HTTP_UNPROCESSABLE_ENTITY
                    );
                }

                foreach ($order->items as $item) {
                    $variant = $this->variantRepository->findForUpdate((int) $item->product_variant_id);

                    if ($variant) {
                        $variant->stock_quantity = (int) $variant->stock_quantity + (int) $item->quantity;
                        $this->variantRepository->save($variant);
                    }
                }

                $this->orderRepository->updateStatus($order, 'REFUNDED');

                return $this->orderRepository->findByIdWithDetails($order->id);
            }, 3);
        } catch (DomainException $e) {
            throw $e;
        } catch (\Throwable $e) {
            Log::error('RefundOrder failed', [
                'order_id' => $orderId,
                'user_id' => $userId,
                'error' => $e->getMessage(),
            ]);

            throw new DomainException('Unexpected error while refunding order', 500);
        }
    }
}
