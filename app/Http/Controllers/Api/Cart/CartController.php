<?php

namespace App\Http\Controllers\Api\Cart;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cart\AddToCartRequest;
use App\Http\Requests\Cart\UpdateCartItemRequest;
use App\Repositories\Cart\CartRepositoryInterface;
use App\Services\Cart\AddToCartService;
use App\Services\Cart\RemoveCartItemService;
use App\Services\Cart\UpdateCartItemService;
use Illuminate\Http\JsonResponse;

class CartController extends Controller
{
    public function show(CartRepositoryInterface $cartRepository): JsonResponse
    {
        $cart = $cartRepository->getActiveCartWithItems((int) auth()->id());

        return response()->json(['data' => $cart], 200);
    }

    public function add(AddToCartRequest $request, AddToCartService $service): JsonResponse
    {
        $payload = $request->validated();

        $cart = $service->execute([
            'user_id' => (int) auth()->id(),
            'variant_id' => (int) $payload['variant_id'],
            'quantity' => (int) $payload['quantity'],
        ]);

        return response()->json(['data' => $cart], 200);
    }

    public function update(int $variantId, UpdateCartItemRequest $request, UpdateCartItemService $service): JsonResponse
    {
        $payload = $request->validated();

        $cart = $service->execute([
            'user_id' => (int) auth()->id(),
            'variant_id' => (int) $variantId,
            'quantity' => (int) $payload['quantity'],
        ]);

        return response()->json(['data' => $cart], 200);
    }

    public function remove(int $variantId, RemoveCartItemService $service): JsonResponse
    {
        $cart = $service->execute([
            'user_id' => (int) auth()->id(),
            'variant_id' => (int) $variantId,
        ]);

        return response()->json(['data' => $cart], 200);
    }
}