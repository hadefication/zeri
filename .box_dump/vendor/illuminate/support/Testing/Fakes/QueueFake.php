<?php

namespace Illuminate\Support\Testing\Fakes;

use BadMethodCallException;
use Closure;
use Illuminate\Contracts\Queue\Queue;
use Illuminate\Events\CallQueuedListener;
use Illuminate\Queue\CallQueuedClosure;
use Illuminate\Queue\QueueManager;
use Illuminate\Support\Collection;
use Illuminate\Support\Traits\ReflectsClosures;
use PHPUnit\Framework\Assert as PHPUnit;

/**
@phpstan-type
*/
class QueueFake extends QueueManager implements Fake, Queue
{
use ReflectsClosures;






public $queue;






protected $jobsToFake;






protected $jobsToBeQueued;






protected $jobs = [];






protected $rawPushes = [];






protected bool $serializeAndRestore = false;








public function __construct($app, $jobsToFake = [], $queue = null)
{
parent::__construct($app);

$this->jobsToFake = Collection::wrap($jobsToFake);
$this->jobsToBeQueued = new Collection;
$this->queue = $queue;
}







public function except($jobsToBeQueued)
{
$this->jobsToBeQueued = Collection::wrap($jobsToBeQueued)->merge($this->jobsToBeQueued);

return $this;
}








public function assertPushed($job, $callback = null)
{
if ($job instanceof Closure) {
[$job, $callback] = [$this->firstClosureParameterType($job), $job];
}

if (is_numeric($callback)) {
return $this->assertPushedTimes($job, $callback);
}

PHPUnit::assertTrue(
$this->pushed($job, $callback)->count() > 0,
"The expected [{$job}] job was not pushed."
);
}








protected function assertPushedTimes($job, $times = 1)
{
$count = $this->pushed($job)->count();

PHPUnit::assertSame(
$times, $count,
"The expected [{$job}] job was pushed {$count} times instead of {$times} times."
);
}









public function assertPushedOn($queue, $job, $callback = null)
{
if ($job instanceof Closure) {
[$job, $callback] = [$this->firstClosureParameterType($job), $job];
}

$this->assertPushed($job, function ($job, $pushedQueue) use ($callback, $queue) {
if ($pushedQueue !== $queue) {
return false;
}

return $callback ? $callback(...func_get_args()) : true;
});
}









public function assertPushedWithChain($job, $expectedChain = [], $callback = null)
{
PHPUnit::assertTrue(
$this->pushed($job, $callback)->isNotEmpty(),
"The expected [{$job}] job was not pushed."
);

PHPUnit::assertTrue(
(new Collection($expectedChain))->isNotEmpty(),
'The expected chain can not be empty.'
);

$this->isChainOfObjects($expectedChain)
? $this->assertPushedWithChainOfObjects($job, $expectedChain, $callback)
: $this->assertPushedWithChainOfClasses($job, $expectedChain, $callback);
}








public function assertPushedWithoutChain($job, $callback = null)
{
PHPUnit::assertTrue(
$this->pushed($job, $callback)->isNotEmpty(),
"The expected [{$job}] job was not pushed."
);

$this->assertPushedWithChainOfClasses($job, [], $callback);
}









protected function assertPushedWithChainOfObjects($job, $expectedChain, $callback)
{
$chain = (new Collection($expectedChain))->map(fn ($job) => serialize($job))->all();

PHPUnit::assertTrue(
$this->pushed($job, $callback)->filter(fn ($job) => $job->chained == $chain)->isNotEmpty(),
'The expected chain was not pushed.'
);
}









protected function assertPushedWithChainOfClasses($job, $expectedChain, $callback)
{
$matching = $this->pushed($job, $callback)->map->chained->map(function ($chain) {
return (new Collection($chain))->map(function ($job) {
return get_class(unserialize($job));
});
})->filter(function ($chain) use ($expectedChain) {
return $chain->all() === $expectedChain;
});

PHPUnit::assertTrue(
$matching->isNotEmpty(), 'The expected chain was not pushed.'
);
}







public function assertClosurePushed($callback = null)
{
$this->assertPushed(CallQueuedClosure::class, $callback);
}







public function assertClosureNotPushed($callback = null)
{
$this->assertNotPushed(CallQueuedClosure::class, $callback);
}







protected function isChainOfObjects($chain)
{
return ! (new Collection($chain))->contains(fn ($job) => ! is_object($job));
}








public function assertNotPushed($job, $callback = null)
{
if ($job instanceof Closure) {
[$job, $callback] = [$this->firstClosureParameterType($job), $job];
}

PHPUnit::assertCount(
0, $this->pushed($job, $callback),
"The unexpected [{$job}] job was pushed."
);
}







public function assertCount($expectedCount)
{
$actualCount = (new Collection($this->jobs))->flatten(1)->count();

PHPUnit::assertSame(
$expectedCount, $actualCount,
"Expected {$expectedCount} jobs to be pushed, but found {$actualCount} instead."
);
}






public function assertNothingPushed()
{
$pushedJobs = implode("\n- ", array_keys($this->jobs));

PHPUnit::assertEmpty($this->jobs, "The following jobs were pushed unexpectedly:\n\n- $pushedJobs\n");
}








public function pushed($job, $callback = null)
{
if (! $this->hasPushed($job)) {
return new Collection;
}

$callback = $callback ?: fn () => true;

return (new Collection($this->jobs[$job]))->filter(
fn ($data) => $callback($data['job'], $data['queue'], $data['data'])
)->pluck('job');
}







public function pushedRaw($callback = null)
{
$callback ??= static fn () => true;

return (new Collection($this->rawPushes))->filter(fn ($data) => $callback($data['payload'], $data['queue'], $data['options']));
}








public function listenersPushed($listenerClass, $callback = null)
{
if (! $this->hasPushed(CallQueuedListener::class)) {
return new Collection;
}

$collection = (new Collection($this->jobs[CallQueuedListener::class]))
->filter(fn ($data) => $data['job']->class === $listenerClass);

if ($callback) {
$collection = $collection->filter(fn ($data) => $callback($data['job']->data[0] ?? null, $data['job'], $data['queue'], $data['data']));
}

return $collection->pluck('job');
}







public function hasPushed($job)
{
return isset($this->jobs[$job]) && ! empty($this->jobs[$job]);
}







public function connection($value = null)
{
return $this;
}







public function size($queue = null)
{
return (new Collection($this->jobs))
->flatten(1)
->filter(fn ($job) => $job['queue'] === $queue)
->count();
}







public function pendingSize($queue = null)
{
return $this->size($queue);
}







public function delayedSize($queue = null)
{
return 0;
}







public function reservedSize($queue = null)
{
return 0;
}







public function creationTimeOfOldestPendingJob($queue = null)
{
return null;
}









public function push($job, $data = '', $queue = null)
{
if ($this->shouldFakeJob($job)) {
if ($job instanceof Closure) {
$job = CallQueuedClosure::create($job);
}

$this->jobs[is_object($job) ? get_class($job) : $job][] = [
'job' => $this->serializeAndRestore ? $this->serializeAndRestoreJob($job) : $job,
'queue' => $queue,
'data' => $data,
];
} else {
is_object($job) && isset($job->connection)
? $this->queue->connection($job->connection)->push($job, $data, $queue)
: $this->queue->push($job, $data, $queue);
}
}







public function shouldFakeJob($job)
{
if ($this->shouldDispatchJob($job)) {
return false;
}

if ($this->jobsToFake->isEmpty()) {
return true;
}

return $this->jobsToFake->contains(
fn ($jobToFake) => $job instanceof ((string) $jobToFake) || $job === (string) $jobToFake
);
}







protected function shouldDispatchJob($job)
{
if ($this->jobsToBeQueued->isEmpty()) {
return false;
}

return $this->jobsToBeQueued->contains(
fn ($jobToQueue) => $job instanceof ((string) $jobToQueue)
);
}









public function pushRaw($payload, $queue = null, array $options = [])
{
$this->rawPushes[] = [
'payload' => $payload,
'queue' => $queue,
'options' => $options,
];
}










public function later($delay, $job, $data = '', $queue = null)
{
return $this->push($job, $data, $queue);
}









public function pushOn($queue, $job, $data = '')
{
return $this->push($job, $data, $queue);
}










public function laterOn($queue, $delay, $job, $data = '')
{
return $this->push($job, $data, $queue);
}







public function pop($queue = null)
{

}









public function bulk($jobs, $data = '', $queue = null)
{
foreach ($jobs as $job) {
$this->push($job, $data, $queue);
}
}






public function pushedJobs()
{
return $this->jobs;
}






public function rawPushes()
{
return $this->rawPushes;
}







public function serializeAndRestore(bool $serializeAndRestore = true)
{
$this->serializeAndRestore = $serializeAndRestore;

return $this;
}







protected function serializeAndRestoreJob($job)
{
return unserialize(serialize($job));
}






public function getConnectionName()
{

}







public function setConnectionName($name)
{
return $this;
}










public function __call($method, $parameters)
{
throw new BadMethodCallException(sprintf(
'Call to undefined method %s::%s()', static::class, $method
));
}
}
