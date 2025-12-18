<?php

namespace App\Http\Controllers\Api\Order;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\PayOrderRequest;
use App\Services\Interfaces\PaymentServiceInterface;
use Illuminate\Http\JsonResponse;


class PaymentController extends Controller
{
    public function __construct(
        private readonly PaymentServiceInterface $paymentService
    ) {
    }

    public function pay(PayOrderRequest $request): JsonResponse
    {
        try {
            $payload = $request->validated();

            $result = $this->paymentService->execute([
                'user_id' => (int) auth()->id(),
                'order_id' => (int) $payload['order_id'],
                'payment_method' => $payload['payment_method'] ?? null,
                'payment_details' => $payload['payment_details'] ?? null,
            ]);

            return response()->json(['data' => $result], 200);
        } catch (\Throwable $e) {
            return $this->handleException($e, 'PaymentController@pay');
        }
    }
}
