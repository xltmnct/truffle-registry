<?php

namespace App\Jobs;

use App\Helpers\RedisHelper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Redis;

class ImportTruffleBatch implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(private array $batch)
    {

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $outputPath = storage_path('app') . DIRECTORY_SEPARATOR . ProcessTruffle::EXPORT_FILE;
        file_exists(dirname($outputPath)) || mkdir(dirname($outputPath), 0777, true);
        $output = fopen($outputPath, 'a+');

        foreach ($this->batch as $row) {
            if (! RedisHelper::isSetMember(RedisHelper::TRUFFLES_REDIS_KEY, $row[0])) {
                fputcsv($output, [$row[0], $row[1], $row[2], $row[3]]);
                Redis::sadd(RedisHelper::TRUFFLES_REDIS_KEY, $row[0]);
            }
        }

        fclose($output);
    }
}
