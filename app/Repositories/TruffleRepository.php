<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Redis;

class TruffleRepository
{
    public const REDIS_KEY = 'truffles';

    public static function isAlreadyProcessed($sku)
    {
        return Redis::sismember(self::REDIS_KEY, (string)$sku);
    }

    public static function setAdd($sku)
    {
        return Redis::sadd(self::REDIS_KEY, $sku);
    }
}