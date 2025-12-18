<?php

namespace App\Services\Order;
use App\Services\Interfaces\PaymentServiceInterface;

use App\Exceptions\DomainException;
use App\Repositories\Interfaces\OrderRepositoryInterface;
use App\Repositories\Interfaces\PaymentRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class PaymentService implements PaymentServiceInterface
{
    public function __construct(
        private readonly OrderRepositoryInterface $orderRepository,
        private readonly PaymentRepositoryInterface $paymentRepository
    ) {}

    /**
     * payload:
     * - user_id (int)
     * - order_id (int)
     * - payment_method (string|null)
     * - payment_details (array|string|null)
     */
    public function execute(array $data): array
    {
        $userId = (int) $data['user_id'];
        $orderId = (int) $data['order_id'];
        $method = $data['payment_method'] ?? null;
        $details = $data['payment_details'] ?? null;

        try {
            return DB::transaction(function () use ($userId, $orderId, $method, $details) {
                $order = $this->orderRepository->findById($orderId);

                if (!$order) {
                    throw new DomainException('Order not found', Response::HTTP_NOT_FOUND);
                }

                if ((int) $order->user_id !== $userId) {
                    throw new DomainException('Unauthorized', Response::HTTP_FORBIDDEN);
                }

                if ($order->status !== 'PENDING_PAYMENT') {
                    throw new DomainException('Order is not pending payment', Response::HTTP_UNPROCESSABLE_ENTITY);
                }

                // Create payment record
                $transactionId = 'TXN-' . $order->id . '-' . now()->format('YmdHis');
                $payment = $this->paymentRepository->create([
                    'order_id' => $order->id,
                    'payment_method' => $method,
                    'amount' => $order->total_amount,
                    'status' => 'COMPLETED',
                    'transaction_id' => $transactionId,
                    'payment_details' => $details,
                ]);

                // Mark order as PAID
                $this->orderRepository->updateStatus($order, 'PAID');

                $fresh = $this->orderRepository->findByIdWithDetails($order->id);

                return [
                    'payment' => $payment,
                    'order' => $fresh,
                ];
            }, 3);
        } catch (DomainException $e) {
            throw $e;
        } catch (\Throwable $e) {
            Log::error('Payment failed', [
                'user_id' => $userId,
                'order_id' => $orderId,
                'error' => $e->getMessage(),
            ]);

            throw new DomainException('Unexpected error while processing payment', 500);
        }
    }
}
