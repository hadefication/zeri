<?php

namespace Illuminate\Contracts\Bus;

interface QueueingDispatcher extends Dispatcher
{






public function findBatch(string $batchId);







public function batch($jobs);







public function dispatchToQueue($command);
}
