<?php

namespace Feature\Jobs;

use App\Jobs\ProcessTruffle;
use App\Models\Truffle;
use App\Services\TrufflesProcessService;
use Tests\Feature\BaseTestCase;

// TODO Add check that already marked in Redis truffles doesn't processed
class ProcessJobTest extends BaseTestCase
{
    public function test_truffle_processor_mark_truffle_in_redis()
    {
        // Given a truffle
        $truffle = Truffle::factory()->create();

        // When the truffle processor handles it
        ProcessTruffle::dispatch($truffle);

        // Check truffle marked in Redis
        $truffleService = app(TrufflesProcessService::class);
        $this->assertTrue($truffleService->isAlreadyProcessed($truffle->sku));
    }
}
