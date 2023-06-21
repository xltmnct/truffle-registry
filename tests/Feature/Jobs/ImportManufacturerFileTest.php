<?php

namespace Tests\Feature\Jobs;

use App\Jobs\ImportManufacturerFile;
use App\Models\Truffle;
use App\Services\TrufflesProcessService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\Feature\BaseTestCase;

// TODO check that job doesn't handle already imported csv file
// TODO check that job doesn't handle bad csv file
// TODO check that all truffles data recorded to db correctly
// TODO check that not valid truffles raws in csv file are handled properly
// TODO check that job not creating doubles in db
// TODO check big import file handling
class ImportManufacturerFileTest extends BaseTestCase
{
    public function test_truffle_import()
    {
        // Given a source file
        $skus = [];
        $lines = [];

        for ($i = 0; $i <= 100; $i++) {
            $sku = Str::uuid();
            $csvLine = $sku . ',5,3.1,"2022-12-01 16:43:41"';

            $skus[] = $sku;
            $lines[] = $csvLine;
        }

        $csvData = implode(PHP_EOL, $lines);
        Storage::append(self::FOREIGN_MANUFACTURER_PATH_TEST, $csvData);

        // When the truffle importer handles it
        ImportManufacturerFile::dispatch();

        // Get created truffles
        $createdTruffles = Truffle::whereIn('sku', $skus)->get();
        $createdSkus = $createdTruffles->pluck('sku')->all();

        // Assert that every sku from csv now have a corresponding truffle in db
        $this->assertEquals($skus, $createdSkus);

        // Check all truffles sku saved in Redis
        $truffleService = app(TrufflesProcessService::class);
        foreach ($createdSkus as $createdSku) {
            $this->assertTrue($truffleService->isAlreadyProcessed($createdSku));
        }

        Storage::delete(self::FOREIGN_MANUFACTURER_PATH_TEST);
    }
}
