<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Redis;

class RedisHelper
{
    const TRUFFLES_REDIS_KEY = 'truffles';

    public static function isSetMember($key, $value): bool
    {
        return Redis::sismember($key, (string)$value);
    }
}
