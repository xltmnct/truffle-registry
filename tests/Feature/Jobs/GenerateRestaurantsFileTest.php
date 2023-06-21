<?php

namespace Tests\Feature\Jobs;

use App\Jobs\GenerateRestaurantsFile;
use App\Models\Truffle;
use Illuminate\Support\Facades\Storage;
use Tests\Feature\BaseTestCase;

// TODO Add checking all values in csv are correct
// TODO Add check that data updating correctly if csv already exists
// TODO Add check that during updating job is writing in file only new rows
class GenerateRestaurantsFileTest extends BaseTestCase
{
    public function test_new_csv_file_generation()
    {
        Storage::disk(self::STORAGE_DISK_TEST)
            ->delete(self::FOREIGN_RESTAURANT_PATH_TEST);
        Storage::disk(self::STORAGE_DISK_TEST)
            ->delete(self::FOREIGN_RESTAURANT_TEMP_PATH_TEST);

        // Create truffles
        $truffles = Truffle::factory()
            ->count(20)
            ->create();

        // When the truffle importer handles it
        GenerateRestaurantsFile::dispatch();

        $csvData = Storage::disk(self::STORAGE_DISK_TEST)->get(self::FOREIGN_RESTAURANT_PATH_TEST);
        $rows = explode("\n", $csvData);

        $this->assertCount($truffles->count(), $rows);

        // Check all created truffles were recorded in csv
        foreach ($rows as $row) {
            $rowValues = str_getcsv($row);
            $this->assertTrue($truffles->contains('sku', $rowValues[0]));
        }
    }
}
