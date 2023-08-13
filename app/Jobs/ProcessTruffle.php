<?php

namespace App\Jobs;

use App\Helpers\RedisHelper;
use App\Models\Truffle;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Redis;

class ProcessTruffle implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public const EXPORT_FILE = 'export.csv';

    protected $truffle;

    public function __construct(Truffle $truffle)
    {
        $this->truffle = $truffle;
    }

    public function handle()
    {
        if (RedisHelper::isSetMember(RedisHelper::TRUFFLES_REDIS_KEY, $this->truffle->sku)) {
            return;
        }

        $path = storage_path('app') . DIRECTORY_SEPARATOR . self::EXPORT_FILE;
        file_exists(dirname($path)) || mkdir(dirname($path), 0777, true);
        $output = fopen($path, 'a+');

        extract($this->truffle->toArray());
        fputcsv($output, [$sku, $weight, $price, $expires_at]);
        Redis::sadd(RedisHelper::TRUFFLES_REDIS_KEY, $sku);

        fclose($output);
    }
}
