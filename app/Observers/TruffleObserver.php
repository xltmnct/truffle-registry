<?php

namespace App\Observers;

use App\Jobs\ProcessTruffle;
use App\Models\Truffle;

class TruffleObserver
{
    public function created(Truffle $truffle): void
    {
        ProcessTruffle::dispatch($truffle);
    }
}
