<?php

namespace Illuminate\Bus;

use Illuminate\Container\Container;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Throwable;

class ChainedBatch implements ShouldQueue
{
use Batchable, Dispatchable, InteractsWithQueue, Queueable;






public Collection $jobs;






public string $name;






public array $options;






public function __construct(PendingBatch $batch)
{
$this->jobs = static::prepareNestedBatches($batch->jobs);

$this->name = $batch->name;
$this->options = $batch->options;
}







public static function prepareNestedBatches(Collection $jobs): Collection
{
return $jobs->map(fn ($job) => match (true) {
is_array($job) => static::prepareNestedBatches(new Collection($job))->all(),
$job instanceof Collection => static::prepareNestedBatches($job),
$job instanceof PendingBatch => new ChainedBatch($job),
default => $job,
});
}






public function handle()
{
$this->attachRemainderOfChainToEndOfBatch(
$this->toPendingBatch()
)->dispatch();
}






public function toPendingBatch()
{
$batch = Container::getInstance()->make(Dispatcher::class)->batch($this->jobs);

$batch->name = $this->name;
$batch->options = $this->options;

if ($this->queue) {
$batch->onQueue($this->queue);
}

if ($this->connection) {
$batch->onConnection($this->connection);
}

foreach ($this->chainCatchCallbacks ?? [] as $callback) {
$batch->catch(function (Batch $batch, ?Throwable $exception) use ($callback) {
if (! $batch->allowsFailures()) {
$callback($exception);
}
});
}

return $batch;
}







protected function attachRemainderOfChainToEndOfBatch(PendingBatch $batch)
{
if (! empty($this->chained)) {
$next = unserialize(array_shift($this->chained));

$next->chained = $this->chained;

$next->onConnection($next->connection ?: $this->chainConnection);
$next->onQueue($next->queue ?: $this->chainQueue);

$next->chainConnection = $this->chainConnection;
$next->chainQueue = $this->chainQueue;
$next->chainCatchCallbacks = $this->chainCatchCallbacks;

$batch->finally(function (Batch $batch) use ($next) {
if (! $batch->cancelled()) {
Container::getInstance()->make(Dispatcher::class)->dispatch($next);
}
});

$this->chained = [];
}

return $batch;
}
}
