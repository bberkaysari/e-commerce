<?php

namespace App\Http\Controllers\Api\Cart;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cart\AddToCartRequest;
use App\Http\Requests\Cart\UpdateCartItemRequest;
use App\Services\Interfaces\AddToCartServiceInterface;
use App\Services\Interfaces\CartServiceInterface;
use App\Services\Interfaces\RemoveCartItemServiceInterface;
use App\Services\Interfaces\UpdateCartItemServiceInterface;
use Illuminate\Http\JsonResponse;


class CartController extends Controller
{
    public function __construct(
        private readonly CartServiceInterface $cartService,
        private readonly AddToCartServiceInterface $addToCartService,
        private readonly UpdateCartItemServiceInterface $updateCartItemService,
        private readonly RemoveCartItemServiceInterface $removeCartItemService,
    ) {
    }

    public function show(): JsonResponse
    {
        try {
            $cart = $this->cartService->getActiveCart((int) auth()->id());

            return response()->json(['data' => $cart], 200);
        } catch (\Throwable $e) {
            return $this->handleException($e, 'CartController@show');
        }
    }

    public function add(AddToCartRequest $request): JsonResponse
    {
        try {
            $payload = $request->validated();

            $cart = $this->addToCartService->execute([
                'user_id' => (int) auth()->id(),
                'variant_id' => (int) $payload['variant_id'],
                'quantity' => (int) $payload['quantity'],
            ]);

            return response()->json(['data' => $cart], 200);
        } catch (\Throwable $e) {
            return $this->handleException($e, 'CartController@add');
        }
    }

    public function update(int $variantId, UpdateCartItemRequest $request): JsonResponse
    {
        try {
            $payload = $request->validated();

            $cart = $this->updateCartItemService->execute([
                'user_id' => (int) auth()->id(),
                'variant_id' => (int) $variantId,
                'quantity' => (int) $payload['quantity'],
            ]);

            return response()->json(['data' => $cart], 200);
        } catch (\Throwable $e) {
            return $this->handleException($e, 'CartController@update');
        }
    }

    public function remove(int $variantId): JsonResponse
    {
        try {
            $cart = $this->removeCartItemService->execute([
                'user_id' => (int) auth()->id(),
                'variant_id' => (int) $variantId,
            ]);

            return response()->json(['data' => $cart], 200);
        } catch (\Throwable $e) {
            return $this->handleException($e, 'CartController@remove');
        }
    }
}
