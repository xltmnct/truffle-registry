<?php

namespace App\Repositories\Eloquent;

use App\Models\Truffle;

class TruffleRepository
{
    public function create(array $attributes): Truffle
    {
        return Truffle::query()->create($attributes);
    }
}
