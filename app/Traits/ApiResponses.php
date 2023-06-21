<?php

namespace App\Traits;

use App\Exceptions\ApiErrorException;
use Illuminate\Http\JsonResponse;

trait ApiResponses
{
    /**
     * @param int $code
     * @param array $data
     * @return JsonResponse
     */
    public function apiResponseSuccess(int $code, array $data = [], string $message = ''): JsonResponse
    {
        $response = ['status' => 'Success'];

        if (!empty($data)) {
            $response['data'] = $data;
        }
        if (!empty($message)) {
            $response['message'] = $message;
        }

        return response()->json($response, $code);
    }

    /**
     * @param int $code
     * @param string $message
     * @param array $errors
     * @return JsonResponse
     */
    protected function apiResponseFail(int $code, string $message = '', array $errors = []): JsonResponse
    {
        $response = ['status' => 'Fail'];

        if (!empty($message)) {
            $response['message'] = $message;
        }
        if (!empty($errors)) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $code);
    }

    protected function apiResponseFromApiErrorException(ApiErrorException $e): JsonResponse
    {
        $response = ['status' => 'Fail'];

        if (!empty($e->getMessage())) {
            $response['message'] = $e->getMessage();
        }
        if (!empty($e->getErrorData())) {
            $response['errors'] = $e->getErrorData();
        }

        return response()->json($response, $e->getStatusCode());
    }
}
