<?php

use App\Exceptions\DomainException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->api(prepend: [
            \Illuminate\Routing\Middleware\ThrottleRequests::class.':api',
        ]);
    })
    ->withExceptions(function ($exceptions) {
        $exceptions->render(function (DomainException $e, Request $request) {
            if ($request->is('api/*')) {
                $status = $e->getCode();
                if (!is_int($status) || $status < 100 || $status > 599) {
                    $status = Response::HTTP_BAD_REQUEST;
                }
    
                return response()->json([
                    'error' => [
                        'message' => $e->getMessage(),
                    ],
                ], $status);
            }
        });
    })->create();
