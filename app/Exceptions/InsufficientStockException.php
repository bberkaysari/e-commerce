<?php

namespace App\Exceptions;

class InsufficientStockException extends DomainException
{
    public function __construct(int $variantId, int $requested, int $available)
    {
        parent::__construct(
            "Insufficient stock for variant {$variantId}. Requested: {$requested}, Available: {$available}",
            422
        );
    }
}
