<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTruffleRequest;
use App\Jobs\ProcessTruffle;
use App\Models\Truffle;
use App\Traits\ApiResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class TruffleController extends BaseController
{
    use ApiResponses;

    public function store(StoreTruffleRequest $request): JsonResponse
    {
        $now = now();
        $data = [
            'user_id'       => Auth::id(),
            'sku'           => Str::uuid(),
            'weight'        => $request->weight,
            'price'         => (float) $request->price,
            'created_at'    => $now->toDateTimeString(),
            'expires_at'    => $now->addMonth(),
        ];

        try {
            $truffle = Truffle::create($data);
            ProcessTruffle::dispatchAfterResponse($truffle);

            return $this->apiResponseSuccess(JsonResponse::HTTP_CREATED, [
                'sku' => $truffle->sku,
            ]);
        } catch (Throwable $e) {
            Log::error(
                'Error during truffle storing: ' . $e->getMessage(),
                $data
            );

            return $this->apiResponseFail(JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
