<?php

namespace App\Exceptions;

class InactiveVariantException extends DomainException
{
    public function __construct(int $variantId)
    {
        parent::__construct("Variant is inactive: {$variantId}", 422);
    }
}