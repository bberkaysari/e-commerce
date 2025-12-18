<?php

namespace App\Services\Interfaces;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ProductServiceInterface
{
    public function list(array $filters, int $perPage = 15): LengthAwarePaginator;

    public function create(array $data): mixed;

    public function update(int $id, array $data): mixed;

    public function findById(int $id): mixed;
}