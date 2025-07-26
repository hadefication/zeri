<?php

namespace Illuminate\Console\Scheduling;

use BadMethodCallException;
use Closure;
use DateTimeInterface;
use Illuminate\Bus\UniqueLock;
use Illuminate\Console\Application;
use Illuminate\Container\Container;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\CallQueuedClosure;
use Illuminate\Support\Collection;
use Illuminate\Support\ProcessUtils;
use Illuminate\Support\Traits\Macroable;
use RuntimeException;

use function Illuminate\Support\enum_value;

/**
@mixin
*/
class Schedule
{
use Macroable {
__call as macroCall;
}

const SUNDAY = 0;

const MONDAY = 1;

const TUESDAY = 2;

const WEDNESDAY = 3;

const THURSDAY = 4;

const FRIDAY = 5;

const SATURDAY = 6;






protected $events = [];






protected $eventMutex;






protected $schedulingMutex;






protected $timezone;






protected $dispatcher;






protected $mutexCache = [];






protected $attributes;






protected array $groupStack = [];








public function __construct($timezone = null)
{
$this->timezone = $timezone;

if (! class_exists(Container::class)) {
throw new RuntimeException(
'A container implementation is required to use the scheduler. Please install the illuminate/container package.'
);
}

$container = Container::getInstance();

$this->eventMutex = $container->bound(EventMutex::class)
? $container->make(EventMutex::class)
: $container->make(CacheEventMutex::class);

$this->schedulingMutex = $container->bound(SchedulingMutex::class)
? $container->make(SchedulingMutex::class)
: $container->make(CacheSchedulingMutex::class);
}








public function call($callback, array $parameters = [])
{
$this->events[] = $event = new CallbackEvent(
$this->eventMutex, $callback, $parameters, $this->timezone
);

$this->mergePendingAttributes($event);

return $event;
}








public function command($command, array $parameters = [])
{
if (class_exists($command)) {
$command = Container::getInstance()->make($command);

return $this->exec(
Application::formatCommandString($command->getName()), $parameters,
)->description($command->getDescription());
}

return $this->exec(
Application::formatCommandString($command), $parameters
);
}









public function job($job, $queue = null, $connection = null)
{
$jobName = $job;

$queue = enum_value($queue);
$connection = enum_value($connection);

if (! is_string($job)) {
$jobName = method_exists($job, 'displayName')
? $job->displayName()
: $job::class;
}

return $this->name($jobName)->call(function () use ($job, $queue, $connection) {
$job = is_string($job) ? Container::getInstance()->make($job) : $job;

if ($job instanceof ShouldQueue) {
$this->dispatchToQueue($job, $queue ?? $job->queue, $connection ?? $job->connection);
} else {
$this->dispatchNow($job);
}
});
}











protected function dispatchToQueue($job, $queue, $connection)
{
if ($job instanceof Closure) {
if (! class_exists(CallQueuedClosure::class)) {
throw new RuntimeException(
'To enable support for closure jobs, please install the illuminate/queue package.'
);
}

$job = CallQueuedClosure::create($job);
}

if ($job instanceof ShouldBeUnique) {
return $this->dispatchUniqueJobToQueue($job, $queue, $connection);
}

$this->getDispatcher()->dispatch(
$job->onConnection($connection)->onQueue($queue)
);
}











protected function dispatchUniqueJobToQueue($job, $queue, $connection)
{
if (! Container::getInstance()->bound(Cache::class)) {
throw new RuntimeException('Cache driver not available. Scheduling unique jobs not supported.');
}

if (! (new UniqueLock(Container::getInstance()->make(Cache::class)))->acquire($job)) {
return;
}

$this->getDispatcher()->dispatch(
$job->onConnection($connection)->onQueue($queue)
);
}







protected function dispatchNow($job)
{
$this->getDispatcher()->dispatchNow($job);
}








public function exec($command, array $parameters = [])
{
if (count($parameters)) {
$command .= ' '.$this->compileParameters($parameters);
}

$this->events[] = $event = new Event($this->eventMutex, $command, $this->timezone);

$this->mergePendingAttributes($event);

return $event;
}









public function group(Closure $events)
{
if ($this->attributes === null) {
throw new RuntimeException('Invoke an attribute method such as Schedule::daily() before defining a schedule group.');
}

$this->groupStack[] = $this->attributes;

$events($this);

array_pop($this->groupStack);
}







protected function mergePendingAttributes(Event $event)
{
if (isset($this->attributes)) {
$this->attributes->mergeAttributes($event);

$this->attributes = null;
}

if (! empty($this->groupStack)) {
$group = end($this->groupStack);

$group->mergeAttributes($event);
}
}







protected function compileParameters(array $parameters)
{
return (new Collection($parameters))->map(function ($value, $key) {
if (is_array($value)) {
return $this->compileArrayInput($key, $value);
}

if (! is_numeric($value) && ! preg_match('/^(-.$|--.*)/i', $value)) {
$value = ProcessUtils::escapeArgument($value);
}

return is_numeric($key) ? $value : "{$key}={$value}";
})->implode(' ');
}








public function compileArrayInput($key, $value)
{
$value = (new Collection($value))->map(function ($value) {
return ProcessUtils::escapeArgument($value);
});

if (str_starts_with($key, '--')) {
$value = $value->map(function ($value) use ($key) {
return "{$key}={$value}";
});
} elseif (str_starts_with($key, '-')) {
$value = $value->map(function ($value) use ($key) {
return "{$key} {$value}";
});
}

return $value->implode(' ');
}








public function serverShouldRun(Event $event, DateTimeInterface $time)
{
return $this->mutexCache[$event->mutexName()] ??= $this->schedulingMutex->create($event, $time);
}







public function dueEvents($app)
{
return (new Collection($this->events))->filter->isDue($app);
}






public function events()
{
return $this->events;
}







public function useCache($store)
{
if ($this->eventMutex instanceof CacheAware) {
$this->eventMutex->useStore($store);
}

if ($this->schedulingMutex instanceof CacheAware) {
$this->schedulingMutex->useStore($store);
}

return $this;
}








protected function getDispatcher()
{
if ($this->dispatcher === null) {
try {
$this->dispatcher = Container::getInstance()->make(Dispatcher::class);
} catch (BindingResolutionException $e) {
throw new RuntimeException(
'Unable to resolve the dispatcher from the service container. Please bind it or install the illuminate/bus package.',
is_int($e->getCode()) ? $e->getCode() : 0, $e
);
}
}

return $this->dispatcher;
}








public function __call($method, $parameters)
{
if (static::hasMacro($method)) {
return $this->macroCall($method, $parameters);
}

if (method_exists(PendingEventAttributes::class, $method)) {
$this->attributes ??= end($this->groupStack) ?: new PendingEventAttributes($this);

return $this->attributes->$method(...$parameters);
}

throw new BadMethodCallException(sprintf(
'Method %s::%s does not exist.', static::class, $method
));
}
}
