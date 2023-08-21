<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Repositories\Eloquent\UserRepository;
use App\Services\AuthService;
use App\Traits\ApiResponder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends BaseController
{
    public function login(LoginRequest $request, AuthService $authService): JsonResponse
    {
        return ApiResponder::success([
            'token' => $authService->login(
                $request->get('email'),
                $request->get('password')
            ),
        ]);
    }
}
