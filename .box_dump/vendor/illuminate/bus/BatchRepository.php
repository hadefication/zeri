<?php

namespace Illuminate\Bus;

use Closure;

interface BatchRepository
{







public function get($limit, $before);







public function find(string $batchId);







public function store(PendingBatch $batch);








public function incrementTotalJobs(string $batchId, int $amount);








public function decrementPendingJobs(string $batchId, string $jobId);








public function incrementFailedJobs(string $batchId, string $jobId);







public function markAsFinished(string $batchId);







public function cancel(string $batchId);







public function delete(string $batchId);







public function transaction(Closure $callback);






public function rollBack();
}
