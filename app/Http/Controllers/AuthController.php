<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Responses\JsonResponseHandler;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController
{
    /**
     * @throws ValidationException
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return JsonResponseHandler::error('The provided credentials are incorrect', 401);
        }

        $token = $user->createToken($user->name.'_'.now(), ['*'], now()->addDays(6))->plainTextToken;

        return JsonResponseHandler::success(['token' => $token]);
    }
}
