<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Services\Interfaces\AuthServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(
        private readonly AuthServiceInterface $authService
    ) {
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            $result = $this->authService->register($request->validated());

            return response()->json([
                'data' => $result,
            ], 201);
        } catch (\Throwable $e) {
            return $this->handleException($e, 'AuthController@register');
        }
    }

    public function login(LoginRequest $request): JsonResponse
    
    {
        try {
            $result = $this->authService->login(
                $request->email,
                $request->password
            );

            if (!$result) {
                return response()->json([
                    'error' => [
                        'message' => 'Gecersiz kimlik bilgileri'
                        
                        
                    ],
                ], 401);
            }

            return response()->json([
                'data' => $result,
                
                
            ]);
        } catch (\Throwable $e) {
            return $this->handleException($e, 'AuthController@login');
        }
    }

    public function logout(Request $request): JsonResponse
    {
        try {
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'data' => [
                    'message' => 'Cikis yapildi',
                    
                    
                ],
            ]);
        } catch (\Throwable $e) {
            return $this->handleException($e, 'AuthController@logout');
        }
    }
}
