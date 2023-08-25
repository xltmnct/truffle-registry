<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class TruffleTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_register_truffle()
    {
        // Given an authenticated user
        $user = User::factory()->create();
        $response = $this->json('post', '/api/auth/login', [
            'email' => $user->email, 'password' => 'password'
        ]);

        Queue::fake();

        // He/she can register a truffle
        $this->withHeaders([
            'Authorization' => 'Bearer ' . $response['data']['token'],
            'Accept' => 'application/json'
        ])->json('post','/api/truffles/create', [
            'weight' => fake()->numberBetween(1, 30),
            'price' => fake()->randomFloat(2, 1, 30),
        ])->assertCreated();
    }
}
