<?php

namespace App\Services\Interfaces;

use App\Models\Cart;

interface RemoveCartItemServiceInterface
{
    public function execute(array $data): ?Cart;
}
