<?php

namespace App\Http\Controllers\Api\Order;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\PayOrderRequest;
use App\Services\Order\PaymentService;
use Illuminate\Http\JsonResponse;


class PaymentController extends Controller
{
    public function pay(PayOrderRequest $request, PaymentService $service): JsonResponse
    {
        $payload = $request->validated();

        $result = $service->execute([
            'user_id' => (int) auth()->id(),
            'order_id' => (int) $payload['order_id'],
            'payment_method' => $payload['payment_method'] ?? null,
            'payment_details' => $payload['payment_details'] ?? null,
        ]);

        return response()->json(['data' => $result], 200);
    }
}