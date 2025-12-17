<?php

namespace App\Exceptions;

class CartEmptyException extends DomainException
{
    public function __construct()
    {
        parent::__construct("Cart is empty", 422);
    }
}
