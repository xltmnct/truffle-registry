<?php

namespace App\Jobs;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ImportTruffle extends BaseTruffleJob
{
    public function handle(): void
    {
        $fileName = config('sftp.sftp_file_import');
        $localFilePath = storage_path('app') . DIRECTORY_SEPARATOR . $fileName;

        try {
            //Get file from manufacturer sftp server and save locally
            $this->getAndCopySftpFile($fileName, $localFilePath);
            $this->processFile($localFilePath);
        } catch (\Exception $e) {
            Log::error('Error processing truffle import: ' . $e->getMessage());
        }
    }

    protected function getAndCopySftpFile(string $sftpFileName, string $localFilePath): void
    {
        $remoteStorageFileStream = Storage::disk('sftp')->readStream($sftpFileName);
        Storage::writeStream($localFilePath, $remoteStorageFileStream);
    }

    protected function processFile(string $filePath): void
    {
        //Use streams to process files, which will help reduce memory usage
        $importStream = Storage::readStream($filePath);
        $exportStream = Storage::readStream($this->getExportFilePath());

        try {
            $this->processAndWriteData($importStream, $exportStream);
        } finally {
            fclose($importStream);
            fclose($exportStream);

            //delete temp local file
            unlink($filePath);
        }
    }

    protected function processAndWriteData($importStream, $exportStream): void
    {
        //max lines for one iteration, it can be changed
        $chunkSize = 1000;

        while (!feof($importStream)) {
            $chunk = [];

            for ($i = 0; $i < $chunkSize; $i++) {
                $data = fgetcsv($importStream, 1000);
                if ($data !== false) {
                    $chunk[] = $data;
                } else {
                    break;
                }
            }

            foreach ($chunk as [$sku, $weight, $price, $expiresAt]) {
                if (!$this->isAlreadyProcessed($sku)) {
                    fputcsv($exportStream, [$sku, $weight, $price, $expiresAt]);
                    $this->setAdd($sku);
                }
            }
        }
    }
}