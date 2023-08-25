<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_get_token()
    {
        $user = User::factory()->create();

        $this->json('post', '/api/auth/login', [
            'email' => $user->email,
            'password' => 'password'
        ])->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(['data' => ['token']]);
    }

    public function test_user_cannot_get_token_with_incorrect_credentials()
    {
        $user = User::factory()->create();

        $this->json('post', '/api/auth/login', [
            'email' => $user->email,
            'password' => 'wrong_password'
        ])->assertUnauthorized();
    }

    public function test_expired_token_is_not_accepted()
    {
        $token = '3|laravel_sanctum_SS9SSJm0UciuFbOxu6fBSe3Ci5RJxoMd4KaeS6f3254bb759';

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json'
        ])->json('post', '/api/truffles/create')
            ->assertUnauthorized();
    }

    public function test_request_without_credentials_is_unauthorized()
    {
        $this->withHeaders([
            'Accept' => 'application/json'
        ])->json('post', '/api/truffles/create')
            ->assertUnauthorized();
    }

    public function test_request_with_invalid_token_is_unauthorized()
    {
        $this->withHeaders([
            'Authorization' => 'Bearer invalid_token',
            'Accept' => 'application/json'
        ])->json('post', '/api/truffles/create')
            ->assertUnauthorized();
    }
}