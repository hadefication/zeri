<?php

namespace Illuminate\Bus;

use Carbon\CarbonImmutable;
use Closure;
use Illuminate\Contracts\Queue\Factory as QueueFactory;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Queue\CallQueuedClosure;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use JsonSerializable;
use Throwable;

class Batch implements Arrayable, JsonSerializable
{





protected $queue;






protected $repository;






public $id;






public $name;






public $totalJobs;






public $pendingJobs;






public $failedJobs;






public $failedJobIds;






public $options;






public $createdAt;






public $cancelledAt;






public $finishedAt;

















public function __construct(
QueueFactory $queue,
BatchRepository $repository,
string $id,
string $name,
int $totalJobs,
int $pendingJobs,
int $failedJobs,
array $failedJobIds,
array $options,
CarbonImmutable $createdAt,
?CarbonImmutable $cancelledAt = null,
?CarbonImmutable $finishedAt = null,
) {
$this->queue = $queue;
$this->repository = $repository;
$this->id = $id;
$this->name = $name;
$this->totalJobs = $totalJobs;
$this->pendingJobs = $pendingJobs;
$this->failedJobs = $failedJobs;
$this->failedJobIds = $failedJobIds;
$this->options = $options;
$this->createdAt = $createdAt;
$this->cancelledAt = $cancelledAt;
$this->finishedAt = $finishedAt;
}






public function fresh()
{
return $this->repository->find($this->id);
}







public function add($jobs)
{
$count = 0;

$jobs = Collection::wrap($jobs)->map(function ($job) use (&$count) {
$job = $job instanceof Closure ? CallQueuedClosure::create($job) : $job;

if (is_array($job)) {
$count += count($job);

return with($this->prepareBatchedChain($job), function ($chain) {
return $chain->first()
->allOnQueue($this->options['queue'] ?? null)
->allOnConnection($this->options['connection'] ?? null)
->chain($chain->slice(1)->values()->all());
});
} else {
$job->withBatchId($this->id);

$count++;
}

return $job;
});

$this->repository->transaction(function () use ($jobs, $count) {
$this->repository->incrementTotalJobs($this->id, $count);

$this->queue->connection($this->options['connection'] ?? null)->bulk(
$jobs->all(),
$data = '',
$this->options['queue'] ?? null
);
});

return $this->fresh();
}







protected function prepareBatchedChain(array $chain)
{
return (new Collection($chain))->map(function ($job) {
$job = $job instanceof Closure ? CallQueuedClosure::create($job) : $job;

return $job->withBatchId($this->id);
});
}






public function processedJobs()
{
return $this->totalJobs - $this->pendingJobs;
}






public function progress()
{
return $this->totalJobs > 0 ? round(($this->processedJobs() / $this->totalJobs) * 100) : 0;
}







public function recordSuccessfulJob(string $jobId)
{
$counts = $this->decrementPendingJobs($jobId);

if ($this->hasProgressCallbacks()) {
$batch = $this->fresh();

(new Collection($this->options['progress']))->each(function ($handler) use ($batch) {
$this->invokeHandlerCallback($handler, $batch);
});
}

if ($counts->pendingJobs === 0) {
$this->repository->markAsFinished($this->id);
}

if ($counts->pendingJobs === 0 && $this->hasThenCallbacks()) {
$batch = $this->fresh();

(new Collection($this->options['then']))->each(function ($handler) use ($batch) {
$this->invokeHandlerCallback($handler, $batch);
});
}

if ($counts->allJobsHaveRanExactlyOnce() && $this->hasFinallyCallbacks()) {
$batch = $this->fresh();

(new Collection($this->options['finally']))->each(function ($handler) use ($batch) {
$this->invokeHandlerCallback($handler, $batch);
});
}
}







public function decrementPendingJobs(string $jobId)
{
return $this->repository->decrementPendingJobs($this->id, $jobId);
}






public function finished()
{
return ! is_null($this->finishedAt);
}






public function hasProgressCallbacks()
{
return isset($this->options['progress']) && ! empty($this->options['progress']);
}






public function hasThenCallbacks()
{
return isset($this->options['then']) && ! empty($this->options['then']);
}






public function allowsFailures()
{
return Arr::get($this->options, 'allowFailures', false) === true;
}






public function hasFailures()
{
return $this->failedJobs > 0;
}








public function recordFailedJob(string $jobId, $e)
{
$counts = $this->incrementFailedJobs($jobId);

if ($counts->failedJobs === 1 && ! $this->allowsFailures()) {
$this->cancel();
}

if ($this->hasProgressCallbacks() && $this->allowsFailures()) {
$batch = $this->fresh();

(new Collection($this->options['progress']))->each(function ($handler) use ($batch, $e) {
$this->invokeHandlerCallback($handler, $batch, $e);
});
}

if ($counts->failedJobs === 1 && $this->hasCatchCallbacks()) {
$batch = $this->fresh();

(new Collection($this->options['catch']))->each(function ($handler) use ($batch, $e) {
$this->invokeHandlerCallback($handler, $batch, $e);
});
}

if ($counts->allJobsHaveRanExactlyOnce() && $this->hasFinallyCallbacks()) {
$batch = $this->fresh();

(new Collection($this->options['finally']))->each(function ($handler) use ($batch, $e) {
$this->invokeHandlerCallback($handler, $batch, $e);
});
}
}







public function incrementFailedJobs(string $jobId)
{
return $this->repository->incrementFailedJobs($this->id, $jobId);
}






public function hasCatchCallbacks()
{
return isset($this->options['catch']) && ! empty($this->options['catch']);
}






public function hasFinallyCallbacks()
{
return isset($this->options['finally']) && ! empty($this->options['finally']);
}






public function cancel()
{
$this->repository->cancel($this->id);
}






public function canceled()
{
return $this->cancelled();
}






public function cancelled()
{
return ! is_null($this->cancelledAt);
}






public function delete()
{
$this->repository->delete($this->id);
}









protected function invokeHandlerCallback($handler, Batch $batch, ?Throwable $e = null)
{
try {
$handler($batch, $e);
} catch (Throwable $e) {
if (function_exists('report')) {
report($e);
}
}
}






public function toArray()
{
return [
'id' => $this->id,
'name' => $this->name,
'totalJobs' => $this->totalJobs,
'pendingJobs' => $this->pendingJobs,
'processedJobs' => $this->processedJobs(),
'progress' => $this->progress(),
'failedJobs' => $this->failedJobs,
'options' => $this->options,
'createdAt' => $this->createdAt,
'cancelledAt' => $this->cancelledAt,
'finishedAt' => $this->finishedAt,
];
}






public function jsonSerialize(): array
{
return $this->toArray();
}







public function __get($key)
{
return $this->options[$key] ?? null;
}
}
