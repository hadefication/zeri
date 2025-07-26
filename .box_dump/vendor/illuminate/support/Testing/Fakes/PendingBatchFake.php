<?php

namespace Illuminate\Support\Testing\Fakes;

use Illuminate\Bus\PendingBatch;
use Illuminate\Support\Collection;

class PendingBatchFake extends PendingBatch
{





protected $bus;







public function __construct(BusFake $bus, Collection $jobs)
{
$this->bus = $bus;
$this->jobs = $jobs;
}






public function dispatch()
{
return $this->bus->recordPendingBatch($this);
}






public function dispatchAfterResponse()
{
return $this->bus->recordPendingBatch($this);
}
}
