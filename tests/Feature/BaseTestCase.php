<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class BaseTestCase extends TestCase
{
    use RefreshDatabase;

    protected const STORAGE_DISK_TEST = 'local_test';
    protected const FOREIGN_MANUFACTURER_PATH_TEST = 'manufacturer/truffles/import_test.csv';
    protected const FOREIGN_RESTAURANT_PATH_TEST = 'restaurant/truffles/export.csv';
    protected const FOREIGN_RESTAURANT_TEMP_PATH_TEST = 'restaurant/truffles/tmp/export_tmp.csv';

    protected function setUp(): void
    {
        parent::setUp();

        // change local disk to test
        Config::set('filesystems.default', self::STORAGE_DISK_TEST);

        // change manufacturer disk and path to test
        Config::set('storages.remote_disks.manufacturer', self::STORAGE_DISK_TEST);
        Config::set('storages.remote_paths.manufacturer.import', self::FOREIGN_MANUFACTURER_PATH_TEST);

        // change restaurant disk and path to test
        Config::set('storages.remote_disks.restaurant', self::STORAGE_DISK_TEST);
        Config::set('storages.remote_paths.restaurant.export', self::FOREIGN_RESTAURANT_PATH_TEST);
        Config::set('storages.remote_paths.restaurant.export_tmp', self::FOREIGN_RESTAURANT_TEMP_PATH_TEST);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // TODO return back to default storage values
    }
}
