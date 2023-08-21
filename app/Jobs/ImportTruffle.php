<?php

namespace App\Jobs;

use Generator;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Redis;

class ImportTruffle implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public const SOURCE_FILE = 'import.csv';
    public const BATCH_SIZE = 1000;

    protected $truffle;

    public function handle()
    {
        $inputPath = storage_path('app') . DIRECTORY_SEPARATOR . self::SOURCE_FILE;
        file_exists(dirname($inputPath)) || mkdir(dirname($inputPath), 0777, true);
        $generator = $this->reader($inputPath);

        $count = 0;
        $batch = [];
        foreach ($generator as $line) {
            if (blank($line)) continue;

            $batch[] = $line;
            $count++;
            if ($count >= self::BATCH_SIZE) {
                dispatch(new ImportTruffleBatch($batch));
                $batch = [];
                $count = 0;
            }
        }

        if (! empty($batch)) {
            dispatch(new ImportTruffleBatch($batch));
        }
    }

    function reader($filePath): Generator
    {
        $file    = fopen($filePath, 'a+');

        while (($data = fgetcsv($file)) !== false) {
            yield $data;
        }

        fclose($file);
    }
}
