<?php

namespace App\Services\Product;

use App\Repositories\Variant\VariantRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class VariantService
{
    public function __construct(
        private readonly VariantRepositoryInterface $variantRepository
    ) {
    }

    public function list(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        return $this->variantRepository->paginate($filters, $perPage);
    }

    public function create(array $data): mixed
    {
        return $this->variantRepository->create([
            'product_id' => $data['product_id'],
            'sku' => $data['sku'],
            'price' => $data['price'],
            'stock_quantity' => $data['stock_quantity'] ?? 0,
            'attributes' => $data['attributes'] ?? [],
            'status' => $data['status'] ?? true,
        ]);
    }

    public function update(int $id, array $data): mixed
    {
        $variant = $this->variantRepository->findById($id);

        if (!$variant) {
            return null;
        }

        return $this->variantRepository->update($variant, [
            'product_id' => $data['product_id'],
            'sku' => $data['sku'],
            'price' => $data['price'],
            'stock_quantity' => $data['stock_quantity'] ?? 0,
            'attributes' => $data['attributes'] ?? [],
            'status' => $data['status'] ?? true,
        ]);
    }

    public function findById(int $id): mixed
    {
        return $this->variantRepository->findById($id);
    }
}
