<?php

namespace App\Jobs;

use App\Models\Truffle;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ProcessTruffle extends BaseTruffleJob
{
    protected Truffle $truffle;

    public function __construct(Truffle $truffle)
    {
        $this->truffle = $truffle;
    }

    public function handle()
    {
        if ($this->isAlreadyProcessed($this->truffle->sku)) {
            return;
        }

        $exportFile = $this->getExportFilePath();
        $this->processAndWriteData($exportFile);

        $this->sendToSftp($exportFile);
    }

    protected function processAndWriteData(string $exportFile): void
    {
        $streamExportFile = Storage::readStream($exportFile);

        try {
            $truffleData = $this->truffle->toArray();
            fputcsv($streamExportFile, array_values($truffleData));
            $this->setAdd($truffleData['sku']);
        } finally {
            fclose($streamExportFile);
        }
    }

    protected function sendToSftp(string $exportFile): void
    {
        $sftpFileName = config('sftp.sftp_file_export');

        try {
            $streamExportFile = Storage::disk('local')->readStream($exportFile);
            Storage::disk('sftp')->writeStream($sftpFileName, $streamExportFile);
        } catch (\Exception $e) {
            Log::error('Error sending truffle data to SFTP: ' . $e->getMessage());
        }
    }
}
