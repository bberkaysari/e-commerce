<?php

namespace App\Http\Controllers\Api\Catalog;

use App\Http\Controllers\Controller;
use App\Http\Requests\Catalog\StoreProductRequest;
use App\Services\Interfaces\ProductServiceInterface;
use Illuminate\Http\JsonResponse;


class ProductController extends Controller
{
    public function __construct(
        private readonly ProductServiceInterface $productService
    ) {
    }

    public function index(): JsonResponse
    {
        try {
            $filters = request()->only(['search', 'status']);
            $products = $this->productService->list($filters);

            return response()->json($products);
        } catch (\Throwable $e) {
            return $this->handleException($e, 'ProductController@index');
        }
    }

    public function store(StoreProductRequest $request): JsonResponse
    {
        try {
            $product = $this->productService->create($request->validated());

            return response()->json(['data' => $product], 201);
        } catch (\Throwable $e) {
            return $this->handleException($e, 'ProductController@store');
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $product = $this->productService->findById($id);

            if (!$product) {
                return response()->json([
                    'error' => [
                        'message' => 'Urun bulunamadi',
                    ],
                ], 404);
            }

            return response()->json(['data' => $product]);
        } catch (\Throwable $e) {
            return $this->handleException($e, 'ProductController@show');
        }
    }

    public function update(int $id, StoreProductRequest $request): JsonResponse
    {
        try {
            $product = $this->productService->update($id, $request->validated());

            if (!$product) {
                return response()->json([
                    'error' => [
                        'message' => 'Urun bulunamadi',
                    ],
                ], 404);
            }

            return response()->json(['data' => $product]);
        } catch (\Throwable $e) {
            return $this->handleException($e, 'ProductController@update');
        }
    }
}
