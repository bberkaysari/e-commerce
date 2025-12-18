<?php

namespace App\Services\Interfaces;

interface AuthServiceInterface
{
    /**
     * @param array{
     *     name: string,
     *     email: string,
     *     password: string
     * } $data
     */
    public function register(array $data): array;

    public function login(string $email, string $password): ?array;
}