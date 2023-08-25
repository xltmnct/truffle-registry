<?php

namespace App\Jobs;

use App\Repositories\TruffleRepository;
use App\Services\FileService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class BaseTruffleJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected function openOrCreateFile($filePath)
    {
        return FileService::openOrCreateFile($filePath);
    }

    protected function getExportFilePath(): string
    {
        return FileService::getExportFilePath();
    }

    protected function getImportFilePath(): string
    {
        return FileService::getImportFilePath();
    }

    protected function isAlreadyProcessed($sku)
    {
        return TruffleRepository::isAlreadyProcessed($sku);
    }

    protected function setAdd($sku)
    {
        return TruffleRepository::setAdd($sku);
    }
}
