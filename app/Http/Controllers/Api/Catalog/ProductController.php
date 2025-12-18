<?php

namespace App\Http\Controllers\Api\Catalog;

use App\Http\Controllers\Controller;
use App\Http\Requests\Catalog\StoreProductRequest;
use App\Services\Product\ProductService;
use Illuminate\Http\JsonResponse;


class ProductController extends Controller
{
    public function __construct(
        private readonly ProductService $productService
    ) {
    }

    public function index(): JsonResponse
    {
        $filters = request()->only(['search', 'status']);
        $products = $this->productService->list($filters);

        return response()->json($products);
    }

    public function store(StoreProductRequest $request): JsonResponse
    {
        $product = $this->productService->create($request->validated());

        return response()->json(['data' => $product], 201);
    }

    public function show(int $id): JsonResponse
    {
        $product = $this->productService->findById($id);

        if (!$product) {
            return response()->json([
                'error' => [
                    'message' => 'Urun bulunamadi',
                ],
            ], 404);
        }

        return response()->json(['data' => $product]);
    }

    public function update(int $id, StoreProductRequest $request): JsonResponse
    {
        $product = $this->productService->update($id, $request->validated());

        if (!$product) {
            return response()->json([
                'error' => [
                    'message' => 'Urun bulunamadi',
                ],
            ], 404);
        }

        return response()->json(['data' => $product]);
    }
}
