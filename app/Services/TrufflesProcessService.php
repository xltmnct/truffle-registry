<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Throwable;

class TrufflesProcessService
{
    private const REDIS_KEY = 'truffles:processed';

    /**
     * @param string $sku
     * @param int $weight
     * @param float $price
     * @param string $expires_at
     * @return void
     * @throws Throwable
     */
    public function execute(string $sku): void
    {
        try {
            if (!$this->isAlreadyProcessed($sku)) {
                $this->markProcessed($sku);
            }
        } catch (Throwable $e) {
            Log::error('Error processing truffle: ' . $e->getMessage(), [
                $sku,
            ]);

            throw $e;
        }
    }

    public function markProcessed(string $sku): void
    {
        Redis::sadd(self::REDIS_KEY, $sku);
    }

    public function isAlreadyProcessed(string $sku): bool
    {
        return Redis::sismember(self::REDIS_KEY, $sku);
    }
}
