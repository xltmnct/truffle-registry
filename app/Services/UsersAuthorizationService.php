<?php

namespace App\Services;

use App\Exceptions\ApiErrorException;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class UsersAuthorizationService
{
    private const TOKEN_TYPE = 'Bearer';

    public function login(string $email, string $password): array
    {
        if (Auth::check()) {
            throw new ApiErrorException(JsonResponse::HTTP_NO_CONTENT,
                'User already authenticated');
        }

        if (!Auth::attempt(['email' => $email, 'password' => $password])) {
            throw new ApiErrorException(JsonResponse::HTTP_UNAUTHORIZED,
                'Email or password does not match with our records');
        }

        $user = User::where('email', $email)->firstOrFail();

        return [
            'token_type' => self::TOKEN_TYPE,
            'token' => $user->createToken('auth-token')->plainTextToken,
        ];
    }
}
