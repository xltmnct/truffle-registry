<?php

namespace App\Services;

use App\Models\Truffle;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

class RegisterFileGeneratorService
{
    private const LAST_RECORDED_TRUFFLE_ID_CACHE_KEY = 'last_recorded_truffle_id';
    private const LOADING_RAWS_CHUNK_SIZE = 500;

    private string $remoteStorageDisk;
    private string $remoteStorageTempPath;
    private string $remoteStoragePath;

    public function __construct()
    {
        $this->remoteStorageDisk = config('storages.remote_disks.restaurant');
        $this->remoteStorageTempPath = config('storages.remote_paths.restaurant.export_tmp');
        $this->remoteStoragePath = config('storages.remote_paths.restaurant.export');
    }

    /**
     * @return void
     * @throws Throwable
     */
    public function execute(): void
    {
        try {
            $lastId = $this->getLastTruffleIdInDb();

            if (is_null($lastId)) {
                return;
            }

            $lastRecordedId = $this->getLastRecordedToFileTruffleId();

            if (is_null($lastRecordedId)) {
                Log::warning("Can't find cached last truffle Id. Generating new CSV file");
                $lastRecordedId = 0;
            }

            if ($lastId > $lastRecordedId) {
                $this->createTempFile();
                $this->recordNewTrufflesDataToTempFile($lastId, $lastRecordedId);
                $this->moveTempFileToOriginalFile();
                $this->saveLastRecordedToFileTruffleId($lastId);
            }
        } catch (Throwable $e) {
            Log::error('Error during generating register file for restaurants: ' . $e->getMessage());

            throw $e;
        }
    }

    private function recordNewTrufflesDataToTempFile(int $lastId, int $lastRecordedId): void
    {
        Truffle::query()
            ->where('id', '<=', $lastId)
            ->where('id', '>', $lastRecordedId)
            ->chunk(self::LOADING_RAWS_CHUNK_SIZE, function (Collection $truffles) {
                $csvLines = [];

                $truffles->each(function (Truffle $truffle) use (&$csvLines) {
                    $csvLines[] = implode(',', $truffle->only([
                        'sku',
                        'weight',
                        'price',
                        'expires_at',
                    ]));
                });

                Storage::disk($this->remoteStorageDisk)
                    ->append($this->remoteStorageTempPath, implode(PHP_EOL, $csvLines));
            });
    }

    private function moveTempFileToOriginalFile(): void
    {
        // TODO check the process of deleting file at the same time this file is downloaded by users
        Storage::disk($this->remoteStorageDisk)
            ->delete($this->remoteStoragePath);
        Storage::disk($this->remoteStorageDisk)
            ->move($this->remoteStorageTempPath, $this->remoteStoragePath);
    }

    private function createTempFile(): void
    {
        Storage::disk($this->remoteStorageDisk)
            ->copy($this->remoteStoragePath, $this->remoteStorageTempPath);
    }

    private function saveLastRecordedToFileTruffleId(int $id): void
    {
        Cache::put(self::LAST_RECORDED_TRUFFLE_ID_CACHE_KEY, $id);
    }

    private function getLastRecordedToFileTruffleId(): ?int
    {
        return Cache::get(self::LAST_RECORDED_TRUFFLE_ID_CACHE_KEY);
    }

    private function getLastTruffleIdInDb(): ?int
    {
        return Truffle::orderBy('id', 'desc')
            ->first()?->id;
    }
}
