<?php

namespace App\Repositories\Interfaces;

interface OrderAddressRepositoryInterface
{
    public function bulkCreate(array $rows): void;
}
