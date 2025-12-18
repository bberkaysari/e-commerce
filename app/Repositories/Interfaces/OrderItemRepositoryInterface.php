<?php

namespace App\Repositories\Interfaces;

interface OrderItemRepositoryInterface
{
    public function bulkCreate(array $rows): void;
}
