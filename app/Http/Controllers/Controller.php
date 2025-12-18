<?php

namespace App\Http\Controllers;

use App\Exceptions\DomainException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Throwable;

abstract class Controller
{
    protected function handleException(Throwable $e, string $context): JsonResponse
    {
        if ($e instanceof DomainException) {
            throw $e;
        }

        Log::error($context, [
            'error' => $e->getMessage(),
        ]);

        return response()->json([
            'error' => 'Bir hata oluştu: ' . $e->getMessage(),
        ], 500);
    }
}
