<?php

namespace App\Services;

use App\Exceptions\ApiException;
use App\Repositories\Eloquent\UserRepository;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class AuthService
{
    public function __construct(private UserRepository $userRepository)
    {
    }

    public function login(string $email, string $password): string
    {
        $credentials = [
            'email'    => $email,
            'password' => $password
        ];

        if (! Auth::attempt($credentials)) {
            throw new ApiException('Invalid credentials provided', Response::HTTP_UNAUTHORIZED);
        }

        return $this->userRepository->findByEmail($email)->createToken($email)->plainTextToken;
    }
}
