<?php

namespace Illuminate\Support\Testing\Fakes;

use Carbon\CarbonImmutable;
use Closure;
use Illuminate\Bus\BatchRepository;
use Illuminate\Bus\PendingBatch;
use Illuminate\Bus\UpdatedBatchJobCounts;
use Illuminate\Support\Str;

class BatchRepositoryFake implements BatchRepository
{





protected $batches = [];








public function get($limit, $before)
{
return $this->batches;
}







public function find(string $batchId)
{
return $this->batches[$batchId] ?? null;
}







public function store(PendingBatch $batch)
{
$id = (string) Str::orderedUuid();

$this->batches[$id] = new BatchFake(
$id,
$batch->name,
count($batch->jobs),
count($batch->jobs),
0,
[],
$batch->options,
CarbonImmutable::now(),
null,
null
);

return $this->batches[$id];
}








public function incrementTotalJobs(string $batchId, int $amount)
{

}








public function decrementPendingJobs(string $batchId, string $jobId)
{
return new UpdatedBatchJobCounts;
}








public function incrementFailedJobs(string $batchId, string $jobId)
{
return new UpdatedBatchJobCounts;
}







public function markAsFinished(string $batchId)
{
if (isset($this->batches[$batchId])) {
$this->batches[$batchId]->finishedAt = now();
}
}







public function cancel(string $batchId)
{
if (isset($this->batches[$batchId])) {
$this->batches[$batchId]->cancel();
}
}







public function delete(string $batchId)
{
unset($this->batches[$batchId]);
}







public function transaction(Closure $callback)
{
return $callback();
}






public function rollBack()
{

}
}
