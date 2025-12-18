<?php

namespace App\Services\Interfaces;

use App\Models\Cart;

interface UpdateCartItemServiceInterface
{
    public function execute(array $data): ?Cart;
}
