<?php

namespace App\Jobs;

use App\Models\Truffle;
use App\Services\TrufflesProcessService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class ProcessTruffle implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private Truffle $truffle;

    private int $tries;
    private int $timeout;

    public function __construct(Truffle $truffle)
    {
        $this->truffle = $truffle;

        $this->tries = 5; // TODO set this to be taken from config and config from .env variable
        $this->timeout = 1000; // TODO set this to be taken from config and config from .env variable
    }

    public function handle(TrufflesProcessService $trufflesProcessService): void
    {
        try {
            $trufflesProcessService->execute(
                $this->truffle->sku,
            );
        } catch (Throwable $e) {
            Log::error('Error during ProcessTruffle job: ' . $e->getMessage());

            $this->fail($e);
        }
    }
}