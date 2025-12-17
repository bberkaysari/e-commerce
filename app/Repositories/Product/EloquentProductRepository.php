<?php

namespace App\Repositories\Product;

use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentProductRepository implements ProductRepositoryInterface
{
    public function paginate(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $q = Product::query();

        if (isset($filters['status'])) {
            $q->where('status', (bool)$filters['status']);
        }

        if (!empty($filters['search'])) {
            $s = trim((string)$filters['search']);
            $q->where('name', 'like', "%{$s}%");
        }

        // N+1 önleme: variant sayısını vs. istersek ekleriz
        return $q->orderByDesc('id')->paginate($perPage);
    }

    public function findById(int $id): ?Product
    {
        return Product::query()->find($id);
    }

    public function create(array $data): Product
    {
        return Product::query()->create($data);
    }

    public function update(Product $product, array $data): Product
    {
        $product->fill($data)->save();
        return $product->fresh();
    }
}