<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterTruffleRequest;
use App\Repositories\Eloquent\TruffleRepository;
use App\Traits\ApiResponder;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Str;

class TruffleController extends BaseController
{
    public function store(RegisterTruffleRequest $request, TruffleRepository $truffleRepository): JsonResponse
    {
        $now = now();
        $truffleRepository->create([
            'sku'        => (string)Str::uuid(),
            'weight'     => $request->get('weight'),
            'price'      => $request->get('price'),
            'created_at' => $now,
            'expires_at' => $now->addMonth()
        ]);

        return ApiResponder::success([], 'Success');
    }
}
