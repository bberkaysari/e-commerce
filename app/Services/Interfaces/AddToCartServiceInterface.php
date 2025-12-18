<?php

namespace App\Services\Interfaces;

use App\Models\Cart;

interface AddToCartServiceInterface
{
    public function execute(array $data): Cart;
}