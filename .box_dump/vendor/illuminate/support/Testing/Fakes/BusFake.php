<?php

namespace Illuminate\Support\Testing\Fakes;

use Closure;
use Illuminate\Bus\BatchRepository;
use Illuminate\Bus\ChainedBatch;
use Illuminate\Bus\PendingBatch;
use Illuminate\Contracts\Bus\QueueingDispatcher;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Traits\ReflectsClosures;
use PHPUnit\Framework\Assert as PHPUnit;
use RuntimeException;

class BusFake implements Fake, QueueingDispatcher
{
use ReflectsClosures;






public $dispatcher;






protected $jobsToFake = [];






protected $jobsToDispatch = [];






protected $batchRepository;






protected $commands = [];






protected $commandsSync = [];






protected $commandsAfterResponse = [];






protected $batches = [];






protected bool $serializeAndRestore = false;








public function __construct(QueueingDispatcher $dispatcher, $jobsToFake = [], ?BatchRepository $batchRepository = null)
{
$this->dispatcher = $dispatcher;
$this->jobsToFake = Arr::wrap($jobsToFake);
$this->batchRepository = $batchRepository ?: new BatchRepositoryFake;
}







public function except($jobsToDispatch)
{
$this->jobsToDispatch = array_merge($this->jobsToDispatch, Arr::wrap($jobsToDispatch));

return $this;
}








public function assertDispatched($command, $callback = null)
{
if ($command instanceof Closure) {
[$command, $callback] = [$this->firstClosureParameterType($command), $command];
}

if (is_numeric($callback)) {
return $this->assertDispatchedTimes($command, $callback);
}

PHPUnit::assertTrue(
$this->dispatched($command, $callback)->count() > 0 ||
$this->dispatchedAfterResponse($command, $callback)->count() > 0 ||
$this->dispatchedSync($command, $callback)->count() > 0,
"The expected [{$command}] job was not dispatched."
);
}








public function assertDispatchedTimes($command, $times = 1)
{
$callback = null;

if ($command instanceof Closure) {
[$command, $callback] = [$this->firstClosureParameterType($command), $command];
}

$count = $this->dispatched($command, $callback)->count() +
$this->dispatchedAfterResponse($command, $callback)->count() +
$this->dispatchedSync($command, $callback)->count();

PHPUnit::assertSame(
$times, $count,
"The expected [{$command}] job was pushed {$count} times instead of {$times} times."
);
}








public function assertNotDispatched($command, $callback = null)
{
if ($command instanceof Closure) {
[$command, $callback] = [$this->firstClosureParameterType($command), $command];
}

PHPUnit::assertTrue(
$this->dispatched($command, $callback)->count() === 0 &&
$this->dispatchedAfterResponse($command, $callback)->count() === 0 &&
$this->dispatchedSync($command, $callback)->count() === 0,
"The unexpected [{$command}] job was dispatched."
);
}






public function assertNothingDispatched()
{
$commandNames = implode("\n- ", array_keys($this->commands));

PHPUnit::assertEmpty($this->commands, "The following jobs were dispatched unexpectedly:\n\n- $commandNames\n");
}








public function assertDispatchedSync($command, $callback = null)
{
if ($command instanceof Closure) {
[$command, $callback] = [$this->firstClosureParameterType($command), $command];
}

if (is_numeric($callback)) {
return $this->assertDispatchedSyncTimes($command, $callback);
}

PHPUnit::assertTrue(
$this->dispatchedSync($command, $callback)->count() > 0,
"The expected [{$command}] job was not dispatched synchronously."
);
}








public function assertDispatchedSyncTimes($command, $times = 1)
{
$callback = null;

if ($command instanceof Closure) {
[$command, $callback] = [$this->firstClosureParameterType($command), $command];
}

$count = $this->dispatchedSync($command, $callback)->count();

PHPUnit::assertSame(
$times, $count,
"The expected [{$command}] job was synchronously pushed {$count} times instead of {$times} times."
);
}








public function assertNotDispatchedSync($command, $callback = null)
{
if ($command instanceof Closure) {
[$command, $callback] = [$this->firstClosureParameterType($command), $command];
}

PHPUnit::assertCount(
0, $this->dispatchedSync($command, $callback),
"The unexpected [{$command}] job was dispatched synchronously."
);
}








public function assertDispatchedAfterResponse($command, $callback = null)
{
if ($command instanceof Closure) {
[$command, $callback] = [$this->firstClosureParameterType($command), $command];
}

if (is_numeric($callback)) {
return $this->assertDispatchedAfterResponseTimes($command, $callback);
}

PHPUnit::assertTrue(
$this->dispatchedAfterResponse($command, $callback)->count() > 0,
"The expected [{$command}] job was not dispatched after sending the response."
);
}








public function assertDispatchedAfterResponseTimes($command, $times = 1)
{
$callback = null;

if ($command instanceof Closure) {
[$command, $callback] = [$this->firstClosureParameterType($command), $command];
}

$count = $this->dispatchedAfterResponse($command, $callback)->count();

PHPUnit::assertSame(
$times, $count,
"The expected [{$command}] job was pushed {$count} times instead of {$times} times."
);
}








public function assertNotDispatchedAfterResponse($command, $callback = null)
{
if ($command instanceof Closure) {
[$command, $callback] = [$this->firstClosureParameterType($command), $command];
}

PHPUnit::assertCount(
0, $this->dispatchedAfterResponse($command, $callback),
"The unexpected [{$command}] job was dispatched after sending the response."
);
}







public function assertChained(array $expectedChain)
{
$command = $expectedChain[0];

$expectedChain = array_slice($expectedChain, 1);

$callback = null;

if ($command instanceof Closure) {
[$command, $callback] = [$this->firstClosureParameterType($command), $command];
} elseif ($command instanceof ChainedBatchTruthTest) {
$instance = $command;

$command = ChainedBatch::class;

$callback = fn ($job) => $instance($job->toPendingBatch());
} elseif (! is_string($command)) {
$instance = $command;

$command = get_class($instance);

$callback = function ($job) use ($instance) {
return serialize($this->resetChainPropertiesToDefaults($job)) === serialize($instance);
};
}

PHPUnit::assertTrue(
$this->dispatched($command, $callback)->isNotEmpty(),
"The expected [{$command}] job was not dispatched."
);

$this->assertDispatchedWithChainOfObjects($command, $expectedChain, $callback);
}






public function assertNothingChained()
{
$this->assertNothingDispatched();
}







protected function resetChainPropertiesToDefaults($job)
{
return tap(clone $job, function ($job) {
$job->chainConnection = null;
$job->chainQueue = null;
$job->chainCatchCallbacks = null;
$job->chained = [];
});
}








public function assertDispatchedWithoutChain($command, $callback = null)
{
if ($command instanceof Closure) {
[$command, $callback] = [$this->firstClosureParameterType($command), $command];
}

PHPUnit::assertTrue(
$this->dispatched($command, $callback)->isNotEmpty(),
"The expected [{$command}] job was not dispatched."
);

$this->assertDispatchedWithChainOfObjects($command, [], $callback);
}









protected function assertDispatchedWithChainOfObjects($command, $expectedChain, $callback)
{
$chain = $expectedChain;

PHPUnit::assertTrue(
$this->dispatched($command, $callback)->filter(function ($job) use ($chain) {
if (count($chain) !== count($job->chained)) {
return false;
}

foreach ($job->chained as $index => $serializedChainedJob) {
if ($chain[$index] instanceof ChainedBatchTruthTest) {
$chainedBatch = unserialize($serializedChainedJob);

if (! $chainedBatch instanceof ChainedBatch ||
! $chain[$index]($chainedBatch->toPendingBatch())) {
return false;
}
} elseif ($chain[$index] instanceof Closure) {
[$expectedType, $callback] = [$this->firstClosureParameterType($chain[$index]), $chain[$index]];

$chainedJob = unserialize($serializedChainedJob);

if (! $chainedJob instanceof $expectedType) {
throw new RuntimeException('The chained job was expected to be of type '.$expectedType.', '.$chainedJob::class.' chained.');
}

if (! $callback($chainedJob)) {
return false;
}
} elseif (is_string($chain[$index])) {
if ($chain[$index] != get_class(unserialize($serializedChainedJob))) {
return false;
}
} elseif (serialize($chain[$index]) != $serializedChainedJob) {
return false;
}
}

return true;
})->isNotEmpty(),
'The expected chain was not dispatched.'
);
}







public function chainedBatch(Closure $callback)
{
return new ChainedBatchTruthTest($callback);
}







public function assertBatched(callable $callback)
{
PHPUnit::assertTrue(
$this->batched($callback)->count() > 0,
'The expected batch was not dispatched.'
);
}







public function assertBatchCount($count)
{
PHPUnit::assertCount(
$count, $this->batches,
);
}






public function assertNothingBatched()
{
$jobNames = (new Collection($this->batches))
->map(fn ($batch) => $batch->jobs->map(fn ($job) => get_class($job)))
->flatten()
->join("\n- ");

PHPUnit::assertEmpty($this->batches, "The following batched jobs were dispatched unexpectedly:\n\n- $jobNames\n");
}






public function assertNothingPlaced()
{
$this->assertNothingDispatched();
$this->assertNothingBatched();
}








public function dispatched($command, $callback = null)
{
if (! $this->hasDispatched($command)) {
return new Collection;
}

$callback = $callback ?: fn () => true;

return (new Collection($this->commands[$command]))->filter(fn ($command) => $callback($command));
}








public function dispatchedSync(string $command, $callback = null)
{
if (! $this->hasDispatchedSync($command)) {
return new Collection;
}

$callback = $callback ?: fn () => true;

return (new Collection($this->commandsSync[$command]))->filter(fn ($command) => $callback($command));
}








public function dispatchedAfterResponse(string $command, $callback = null)
{
if (! $this->hasDispatchedAfterResponse($command)) {
return new Collection;
}

$callback = $callback ?: fn () => true;

return (new Collection($this->commandsAfterResponse[$command]))->filter(fn ($command) => $callback($command));
}







public function batched(callable $callback)
{
if (empty($this->batches)) {
return new Collection;
}

return (new Collection($this->batches))->filter(fn ($batch) => $callback($batch));
}







public function hasDispatched($command)
{
return isset($this->commands[$command]) && ! empty($this->commands[$command]);
}







public function hasDispatchedSync($command)
{
return isset($this->commandsSync[$command]) && ! empty($this->commandsSync[$command]);
}







public function hasDispatchedAfterResponse($command)
{
return isset($this->commandsAfterResponse[$command]) && ! empty($this->commandsAfterResponse[$command]);
}







public function dispatch($command)
{
if ($this->shouldFakeJob($command)) {
$this->commands[get_class($command)][] = $this->getCommandRepresentation($command);
} else {
return $this->dispatcher->dispatch($command);
}
}










public function dispatchSync($command, $handler = null)
{
if ($this->shouldFakeJob($command)) {
$this->commandsSync[get_class($command)][] = $this->getCommandRepresentation($command);
} else {
return $this->dispatcher->dispatchSync($command, $handler);
}
}








public function dispatchNow($command, $handler = null)
{
if ($this->shouldFakeJob($command)) {
$this->commands[get_class($command)][] = $this->getCommandRepresentation($command);
} else {
return $this->dispatcher->dispatchNow($command, $handler);
}
}







public function dispatchToQueue($command)
{
if ($this->shouldFakeJob($command)) {
$this->commands[get_class($command)][] = $this->getCommandRepresentation($command);
} else {
return $this->dispatcher->dispatchToQueue($command);
}
}







public function dispatchAfterResponse($command)
{
if ($this->shouldFakeJob($command)) {
$this->commandsAfterResponse[get_class($command)][] = $this->getCommandRepresentation($command);
} else {
return $this->dispatcher->dispatch($command);
}
}







public function chain($jobs)
{
$jobs = Collection::wrap($jobs);
$jobs = ChainedBatch::prepareNestedBatches($jobs);

return new PendingChainFake($this, $jobs->shift(), $jobs->toArray());
}







public function findBatch(string $batchId)
{
return $this->batchRepository->find($batchId);
}







public function batch($jobs)
{
return new PendingBatchFake($this, Collection::wrap($jobs));
}







public function dispatchFakeBatch($name = '')
{
return $this->batch([])->name($name)->dispatch();
}







public function recordPendingBatch(PendingBatch $pendingBatch)
{
$this->batches[] = $pendingBatch;

return $this->batchRepository->store($pendingBatch);
}







protected function shouldFakeJob($command)
{
if ($this->shouldDispatchCommand($command)) {
return false;
}

if (empty($this->jobsToFake)) {
return true;
}

return (new Collection($this->jobsToFake))
->filter(function ($job) use ($command) {
return $job instanceof Closure
? $job($command)
: $job === get_class($command);
})->isNotEmpty();
}







protected function shouldDispatchCommand($command)
{
return (new Collection($this->jobsToDispatch))
->filter(function ($job) use ($command) {
return $job instanceof Closure
? $job($command)
: $job === get_class($command);
})->isNotEmpty();
}







public function serializeAndRestore(bool $serializeAndRestore = true)
{
$this->serializeAndRestore = $serializeAndRestore;

return $this;
}







protected function serializeAndRestoreCommand($command)
{
return unserialize(serialize($command));
}







protected function getCommandRepresentation($command)
{
return $this->serializeAndRestore ? $this->serializeAndRestoreCommand($command) : $command;
}







public function pipeThrough(array $pipes)
{
$this->dispatcher->pipeThrough($pipes);

return $this;
}







public function hasCommandHandler($command)
{
return $this->dispatcher->hasCommandHandler($command);
}







public function getCommandHandler($command)
{
return $this->dispatcher->getCommandHandler($command);
}







public function map(array $map)
{
$this->dispatcher->map($map);

return $this;
}






public function dispatchedBatches()
{
return $this->batches;
}
}
