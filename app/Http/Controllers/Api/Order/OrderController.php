<?php

namespace App\Http\Controllers\Api\Order;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\CancelOrderRequest;
use App\Http\Requests\Order\CheckoutRequest;
use App\Http\Requests\Order\RefundOrderRequest;
use App\Repositories\Order\OrderRepositoryInterface;
use App\Services\Order\CancelOrderService;
use App\Services\Order\PlaceOrderService;
use App\Services\Order\RefundOrderService;
use Illuminate\Http\JsonResponse;


class OrderController extends Controller
{
    public function checkout(CheckoutRequest $request, PlaceOrderService $service): JsonResponse
    {
        $order = $service->execute([
            'user_id' => (int) auth()->id(),
            'shipping' => (array) $request->shipping,
            'notes' => $request->notes,
        ]);

        return response()->json(['data' => $order], 201);
    }

    public function index(OrderRepositoryInterface $repository): JsonResponse
    {
        $orders = $repository->paginate((int) auth()->id());

        return response()->json($orders, 200);
    }

    public function show(int $id, OrderRepositoryInterface $repository): JsonResponse
    {
        $order = $repository->findByIdWithDetails($id);

        if (!$order) {
            return response()->json([
                'error' => ['message' => 'Order bulunamadi'],
            ], 404);
        }

        // Opsiyonel: sadece kendi order’ını görebilsin
        if ((int) $order->user_id !== (int) auth()->id()) {
            return response()->json([
                'error' => ['message' => 'Unauthorized'],
            ], 403);
        }

        return response()->json(['data' => $order], 200);
    }

    public function cancel(int $id, CancelOrderRequest $request, CancelOrderService $service): JsonResponse
    {
        $order = $service->execute($id, (int) auth()->id());

        return response()->json([
            'data' => $order,
            'message' => 'Order basariyla iptal edildi',
        ], 200);
    }

    public function refund(int $id, RefundOrderRequest $request, RefundOrderService $service): JsonResponse
    {
        $order = $service->execute($id, (int) auth()->id());

        return response()->json([
            'data' => $order,
            'message' => 'Order geri ödeme talebi basariyla oluşturuldu',
        ], 200);
    }
}