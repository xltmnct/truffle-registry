<?php

namespace App\Http\Controllers;

use App\Http\Requests\TruffleRequest;
use App\Jobs\ProcessTruffle;
use App\Models\Truffle;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TruffleController
{
    use DispatchesJobs;

    public function create(TruffleRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $now = now();

            $truffle = Truffle::create([
                'sku' => (string)Str::uuid(),
                'weight' => $request->weight,
                'price' => $request->price,
                'created_at' => $now,
                'expires_at' => $now->addMonth(),
            ]);

            ProcessTruffle::dispatchAfterResponse($truffle);

            DB::commit();

            return response()->json(['status' => 'success'])->setStatusCode(201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
