<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TruffleFactory extends Factory
{
    public function definition()
    {
        return [
            'user_id' => function () {
                return User::factory()->create()->id;
            },
            'sku' => Str::uuid(),
            'weight' => fake()->unique()->numberBetween(1, 9999999),
            'price' => fake()->randomFloat(2, 1, 1000000),
            'created_at' => now(),
            'expires_at' => now()->modify('+1 month')
        ];
    }
}
