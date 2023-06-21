<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Tests\Feature\BaseTestCase;

// TODO Add checks that authenticated user can register truffle
// TODO Add checks that response structure is ok
// TODO Add check that truffle's data saved correctly
// TODO Add check that truffle sku is marked in Redis
class TruffleTest extends BaseTestCase
{
    public function test_authenticated_user_can_register_truffle()
    {
        // Given an authenticated user
        $user = User::factory()->create();
        $response = $this->post(route('auth.login'), [
            'email' => $user->email, 'password' => 'password'
        ]);

        // He/she can register a truffle
        $this->post(route('truffles.store'), [
            'weight' => fake()->numberBetween(1, 30),
            'price' => fake()->randomFloat(2, 1, 30),
        ], [
            'Authorization' => $response['data']['token_type'] . ' ' . $response['data']['token'],
        ])
            ->assertCreated();
    }
}
