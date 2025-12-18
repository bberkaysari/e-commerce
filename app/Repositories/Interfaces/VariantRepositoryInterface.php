<?php

namespace App\Repositories\Interfaces;

use App\Models\ProductVariant;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface VariantRepositoryInterface
{
    public function paginate(array $filters, int $perPage = 15): LengthAwarePaginator;

    public function findById(int $id): ?ProductVariant;

    public function findBySku(string $sku): ?ProductVariant;

    public function findForUpdate(int $id): ?ProductVariant;

    public function create(array $data): ProductVariant;

    public function update(ProductVariant $variant, array $data): ProductVariant;

    public function save(ProductVariant $variant): ProductVariant;
}
