<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Tests\Feature\BaseTestCase;

// TODO Add checks that response structure is ok
// TODO Add checks that user can get token only with correct credentials
// TODO Add checks that authenticated user don't get new token
class AuthTest extends BaseTestCase
{
    public function test_user_can_get_token()
    {
        // Given an existing user
        $user = User::factory()->create();

        // A user who knows their credentials can obtain a token
        $this->post(route('auth.login'), [
            'email' => $user->email,
            'password' => 'password'
        ])->assertOk();
    }
}
