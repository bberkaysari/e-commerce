<?php

namespace App\Http\Controllers\Api\Order;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\CancelOrderRequest;
use App\Http\Requests\Order\CheckoutRequest;
use App\Http\Requests\Order\RefundOrderRequest;
use App\Services\Interfaces\CancelOrderServiceInterface;
use App\Services\Interfaces\OrderQueryServiceInterface;
use App\Services\Interfaces\PlaceOrderServiceInterface;
use App\Services\Interfaces\RefundOrderServiceInterface;
use Illuminate\Http\JsonResponse;


class OrderController extends Controller
{
    public function __construct(
        private readonly PlaceOrderServiceInterface $placeOrderService,
        private readonly CancelOrderServiceInterface $cancelOrderService,
        private readonly RefundOrderServiceInterface $refundOrderService,
        private readonly OrderQueryServiceInterface $orderQueryService,
    ) {
    }

    public function checkout(CheckoutRequest $request): JsonResponse
    {
        try {
            $order = $this->placeOrderService->execute([
                'user_id' => (int) auth()->id(),
                'shipping' => (array) $request->shipping,
                'notes' => $request->notes,
            ]);

            return response()->json(['data' => $order], 201);
        } catch (\Throwable $e) {
            return $this->handleException($e, 'OrderController@checkout');
        }
    }

    public function index(): JsonResponse
    {
        try {
            $orders = $this->orderQueryService->paginateForUser((int) auth()->id());

            return response()->json($orders, 200);
        } catch (\Throwable $e) {
            return $this->handleException($e, 'OrderController@index');
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $order = $this->orderQueryService->findOwnedOrder($id, (int) auth()->id());

            return response()->json(['data' => $order], 200);
        } catch (\Throwable $e) {
            return $this->handleException($e, 'OrderController@show');
        }
    }

    public function cancel(int $id, CancelOrderRequest $request): JsonResponse
    {
        try {
            $order = $this->cancelOrderService->execute($id, (int) auth()->id());

            return response()->json([
                'data' => $order,
                'message' => 'Order basariyla iptal edildi',
            ], 200);
        } catch (\Throwable $e) {
            return $this->handleException($e, 'OrderController@cancel');
        }
    }

    public function refund(int $id, RefundOrderRequest $request): JsonResponse
    {
        try {
            $order = $this->refundOrderService->execute($id, (int) auth()->id());

            return response()->json([
                'data' => $order,
                'message' => 'Order geri ödeme talebi basariyla oluşturuldu',
            ], 200);
        } catch (\Throwable $e) {
            return $this->handleException($e, 'OrderController@refund');
        }
}
}
