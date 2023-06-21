<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

class ManufacturerFilesImporter
{
    private const CSV_ROWS_PER_ITERATION = 1000;
    private const CSV_MIME_TYPE = 'text/csv';
    private const MAXIMUM_FILE_BYTES_SIZE = 100 * 1024 * 1024; // 100mb
    private const CACHE_FILE_TIMESTAMP_KEY = 'remote_file_last_modified';

    /** @var TrufflesProcessService  */
    private TrufflesProcessService $trufflesProcessService;

    private string $localStoragePath;
    private string $remoteStorageDisk;
    private string $remoteStoragePath;

    public function __construct(TrufflesProcessService $trufflesProcessService)
    {
        $this->trufflesProcessService = $trufflesProcessService;

        $this->localStoragePath = config('storages.local_paths.import');
        $this->remoteStorageDisk = config('storages.remote_disks.manufacturer');
        $this->remoteStoragePath = config('storages.remote_paths.manufacturer.import');
    }

    /**
     * @return void
     * @throws Throwable
     */
    public function execute(): void
    {
        try {
            if (
                !$this->checkRemoteStorageFileExists() ||
                !$this->checkRemoteStorageFileIsNew()
            ) {
                return;
            }

            if (!$this->checkRemoteStorageFileIsCorrect()) {
                throw new Exception('Incorrect remote storage file');
            }

            $this->copyFileFromRemoteStorageToLocalStorage();
            $this->handleLocalStorageFile();
            $this->saveHandledRemoteStorageFileDate();
            $this->removeLocalStorageFile();
        } catch (Throwable $e) {
            Log::error('Error during importing manufacturer\'s file: ' . $e->getMessage());

            throw $e;
        }
    }

    private function handleLocalStorageFile(): void
    {
        $fileStream = Storage::readStream($this->localStoragePath);

        while (($line = fgetcsv($fileStream, self::CSV_ROWS_PER_ITERATION)) !== false) {
            // TODO add checking values not null and correct format
            [$sku, $weight, $price, $expiresAt] = $line;

            if (!$this->trufflesProcessService->isAlreadyProcessed($sku)) {
                // TODO record to DB by chunks, by 500 for example
                DB::table('truffles')->insert([
                    'sku' => $sku,
                    'weight' => $weight,
                    'price' => $price,
                    'created_at' => now(),
                    'expires_at' => $expiresAt,
                ]);

                $this->trufflesProcessService->markProcessed($sku);
            }
        }
    }

    private function checkRemoteStorageFileIsNew(): bool
    {
        $timestamp = Storage::disk($this->remoteStorageDisk)
            ->lastModified($this->remoteStoragePath);

        $oldTimestamp = Cache::get(self::CACHE_FILE_TIMESTAMP_KEY);

        return $timestamp !== $oldTimestamp;
    }

    private function saveHandledRemoteStorageFileDate(): void
    {
        $timestamp = Storage::disk($this->remoteStorageDisk)
            ->lastModified($this->remoteStoragePath);

        Cache::put(self::CACHE_FILE_TIMESTAMP_KEY, $timestamp);
    }

    private function checkRemoteStorageFileExists(): bool
    {
        return Storage::disk($this->remoteStorageDisk)
            ->exists($this->remoteStoragePath);
    }

    private function checkRemoteStorageFileIsCorrect(): bool
    {
        $fileMimeType = Storage::disk($this->remoteStorageDisk)
            ->mimeType($this->remoteStoragePath);
        $fileSize = Storage::disk($this->remoteStorageDisk)
            ->size($this->remoteStoragePath);

        return
            $fileMimeType === self::CSV_MIME_TYPE &&
            $fileSize < self::MAXIMUM_FILE_BYTES_SIZE;
    }

    private function copyFileFromRemoteStorageToLocalStorage(): void
    {
        $remoteStorageFileStream = Storage::disk($this->remoteStorageDisk)
            ->readStream($this->remoteStoragePath);

        Storage::writeStream($this->localStoragePath, $remoteStorageFileStream);
    }

    private function removeLocalStorageFile(): void
    {
        Storage::delete($this->localStoragePath);
    }
}
