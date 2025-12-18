<?php

namespace App\Services\Product;

use App\Repositories\Interfaces\ProductRepositoryInterface;
use App\Services\Interfaces\ProductServiceInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProductService implements ProductServiceInterface
{
    public function __construct(
        private readonly ProductRepositoryInterface $productRepository
    ) {
    }

    public function list(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        return $this->productRepository->paginate($filters, $perPage);
    }

    public function create(array $data): mixed
    {
        return $this->productRepository->create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'status' => $data['status'] ?? true,
        ]);
    }

    public function update(int $id, array $data): mixed
    {
        $product = $this->productRepository->findById($id);

        if (!$product) {
            return null;
        }

        return $this->productRepository->update($product, [
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'status' => $data['status'] ?? true,
        ]);
    }

    public function findById(int $id): mixed
    {
        return $this->productRepository->findById($id);
    }
}
