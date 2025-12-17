<?php

namespace App\Exceptions;

class VariantNotFoundException extends DomainException
{
    public function __construct(int $variantId)
    {
        parent::__construct("Variant not found: {$variantId}", 404);
    }
}   