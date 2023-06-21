<?php

namespace App\Http\Controllers;

use App\Exceptions\ApiErrorException;
use App\Http\Requests\LoginRequest;
use App\Services\UsersAuthorizationService;
use App\Traits\ApiResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Log;
use Throwable;

class AuthController extends BaseController
{
    use ApiResponses;

    private UsersAuthorizationService $usersAuthorizationService;

    public function __construct(UsersAuthorizationService $usersAuthorizationService)
    {
        $this->usersAuthorizationService = $usersAuthorizationService;
    }

    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $tokenData = $this->usersAuthorizationService->login(
                $request->get('email'),
                $request->get('password')
            );

            return $this->apiResponseSuccess(JsonResponse::HTTP_OK, $tokenData);
        } catch (ApiErrorException $e) {
            return $this->apiResponseFromApiErrorException($e);
        } catch (Throwable $e) {
            Log::error('Error during logging in: ' . $e->getMessage(), $request->validated());

            return $this->apiResponseFail(JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'Unexpected server error. Please try again later');
        }
    }
}
