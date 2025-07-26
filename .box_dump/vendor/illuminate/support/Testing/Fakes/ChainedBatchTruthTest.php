<?php

namespace Illuminate\Support\Testing\Fakes;

use Closure;

class ChainedBatchTruthTest
{





protected $callback;






public function __construct(Closure $callback)
{
$this->callback = $callback;
}







public function __invoke($pendingBatch)
{
return call_user_func($this->callback, $pendingBatch);
}
}
