<?php

namespace App\Repositories\User;

use App\Models\User;

class EloquentUserRepository implements UserRepositoryInterface
{
    public function create(array $data): User
    {
        return User::create($data);
    }

    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    public function save(User $user): bool
    {
        return $user->save();
    }
}
