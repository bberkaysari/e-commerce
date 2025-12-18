<?php

namespace App\Repositories\Variant;

use App\Models\ProductVariant;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentVariantRepository implements VariantRepositoryInterface
{
    public function paginate(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $q = ProductVariant::query()->with(['product:id,name,status']);

        if (!empty($filters['product_id'])) {
            $q->where('product_id', (int)$filters['product_id']);
        }

        if (isset($filters['status'])) {
            $q->where('status', (bool)$filters['status']);
        }

        if (!empty($filters['search'])) {
            $s = trim((string)$filters['search']);
            $q->where(function ($sub) use ($s) {
                $sub->where('sku', 'like', "%{$s}%");
            });
        }

        return $q->orderByDesc('id')->paginate($perPage);
    }

    public function findById(int $id): ?ProductVariant
    {
        return ProductVariant::query()->find($id);
    }

    public function findBySku(string $sku): ?ProductVariant
    {
        return ProductVariant::query()->where('sku', $sku)->with('product')->first();
    }

    public function findForUpdate(int $id): ?ProductVariant
    {
        return ProductVariant::query()->where('id', $id)->lockForUpdate()->first();
    }

    public function create(array $data): ProductVariant
    {
        return ProductVariant::query()->create($data);
    }

    public function update(ProductVariant $variant, array $data): ProductVariant
    {
        $variant->fill($data)->save();
        return $variant->fresh();
    }

    public function save(ProductVariant $variant): ProductVariant
    {
        $variant->save();
        return $variant->fresh();
    }
}