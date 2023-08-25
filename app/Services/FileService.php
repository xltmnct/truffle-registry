<?php

namespace App\Services;

class FileService
{
    public static function openOrCreateFile($filePath)
    {
        file_exists(dirname($filePath))
        || mkdir($concurrentDirectory = dirname($filePath), 0777, true)
        || is_dir($concurrentDirectory);
        return fopen($filePath, 'ab+');
    }

    public static function getExportFilePath(): string
    {
        return storage_path('app') . DIRECTORY_SEPARATOR . config('sftp.sftp_file_export');
    }

    public static function getImportFilePath(): string
    {
        return storage_path('app') . DIRECTORY_SEPARATOR . config('sftp.sftp_file_import');
    }
}