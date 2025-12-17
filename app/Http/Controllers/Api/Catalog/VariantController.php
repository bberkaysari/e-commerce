<?php

namespace App\Http\Controllers\Api\Catalog;

use App\Http\Controllers\Controller;
use App\Http\Requests\Catalog\StoreVariantRequest;
use App\Services\Product\VariantService;
use Illuminate\Http\JsonResponse;

class VariantController extends Controller
{
    public function __construct(
        private readonly VariantService $variantService
    ) {
    }

    public function index(): JsonResponse
    {
        $filters = request()->only(['product_id', 'status', 'search']);
        $variants = $this->variantService->list($filters);

        return response()->json($variants);
    }

    public function store(StoreVariantRequest $request): JsonResponse
    {
        $variant = $this->variantService->create($request->validated());

        return response()->json(['data' => $variant], 201);
    }

    public function show(int $id): JsonResponse
    {
        $variant = $this->variantService->findById($id);

        if (!$variant) {
            return response()->json([
                'error' => [
                    'message' => 'Variant not found',
                ],
            ], 404);
        }

        return response()->json(['data' => $variant]);
    }

    public function update(int $id, StoreVariantRequest $request): JsonResponse
    {
        $variant = $this->variantService->update($id, $request->validated());

        if (!$variant) {
            return response()->json([
                'error' => [
                    'message' => 'Variant not found',
                ],
            ], 404);
        }

        return response()->json(['data' => $variant]);
    }
}
