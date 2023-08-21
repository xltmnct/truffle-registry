<?php

namespace App\Repositories\Eloquent;

use App\Models\User;

class UserRepository
{
    public function findByEmail(string $email): ?User
    {
        return User::query()->where('email', $email)->first();
    }

    public function create(array $attributes = []): User
    {
        return User::query()->create($attributes);
    }
}
