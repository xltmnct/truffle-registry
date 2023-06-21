<?php

namespace App\Jobs;

use App\Services\RegisterFileGeneratorService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class GenerateRestaurantsFile implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private int $tries;
    private int $timeout;

    public function __construct()
    {
        $this->tries = 5; // TODO set this to be taken from config and config from .env variable
        $this->timeout = 1000; // TODO set this to be taken from config and config from .env variable
    }

    public function handle(RegisterFileGeneratorService $registerFileGeneratorService): void
    {
        try {
            $registerFileGeneratorService->execute();
        } catch (Throwable $e) {
            Log::error('Error during GenerateRestaurantsFile job: ' . $e->getMessage());

            $this->fail($e);
        }
    }
}
