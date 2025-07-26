<?php

namespace Illuminate\Support\Testing\Fakes;

use Closure;
use Illuminate\Container\Container;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\ForwardsCalls;
use Illuminate\Support\Traits\ReflectsClosures;
use PHPUnit\Framework\Assert as PHPUnit;
use ReflectionFunction;

class EventFake implements Dispatcher, Fake
{
use ForwardsCalls, ReflectsClosures;






public $dispatcher;






protected $eventsToFake = [];






protected $eventsToDispatch = [];






protected $events = [];







public function __construct(Dispatcher $dispatcher, $eventsToFake = [])
{
$this->dispatcher = $dispatcher;

$this->eventsToFake = Arr::wrap($eventsToFake);
}







public function except($eventsToDispatch)
{
$this->eventsToDispatch = array_merge(
$this->eventsToDispatch,
Arr::wrap($eventsToDispatch)
);

return $this;
}








public function assertListening($expectedEvent, $expectedListener)
{
foreach ($this->dispatcher->getListeners($expectedEvent) as $listenerClosure) {
$actualListener = (new ReflectionFunction($listenerClosure))
->getStaticVariables()['listener'];

$normalizedListener = $expectedListener;

if (is_string($actualListener) && Str::contains($actualListener, '@')) {
$actualListener = Str::parseCallback($actualListener);

if (is_string($expectedListener)) {
if (Str::contains($expectedListener, '@')) {
$normalizedListener = Str::parseCallback($expectedListener);
} else {
$normalizedListener = [
$expectedListener,
method_exists($expectedListener, 'handle') ? 'handle' : '__invoke',
];
}
}
}

if ($actualListener === $normalizedListener ||
($actualListener instanceof Closure &&
$normalizedListener === Closure::class)) {
PHPUnit::assertTrue(true);

return;
}
}

PHPUnit::assertTrue(
false,
sprintf(
'Event [%s] does not have the [%s] listener attached to it',
$expectedEvent,
print_r($expectedListener, true)
)
);
}








public function assertDispatched($event, $callback = null)
{
if ($event instanceof Closure) {
[$event, $callback] = [$this->firstClosureParameterType($event), $event];
}

if (is_int($callback)) {
return $this->assertDispatchedTimes($event, $callback);
}

PHPUnit::assertTrue(
$this->dispatched($event, $callback)->count() > 0,
"The expected [{$event}] event was not dispatched."
);
}








public function assertDispatchedTimes($event, $times = 1)
{
$count = $this->dispatched($event)->count();

PHPUnit::assertSame(
$times, $count,
"The expected [{$event}] event was dispatched {$count} times instead of {$times} times."
);
}








public function assertNotDispatched($event, $callback = null)
{
if ($event instanceof Closure) {
[$event, $callback] = [$this->firstClosureParameterType($event), $event];
}

PHPUnit::assertCount(
0, $this->dispatched($event, $callback),
"The unexpected [{$event}] event was dispatched."
);
}






public function assertNothingDispatched()
{
$count = count(Arr::flatten($this->events));

$eventNames = (new Collection($this->events))
->map(fn ($events, $eventName) => sprintf(
'%s dispatched %s %s',
$eventName,
count($events),
Str::plural('time', count($events)),
))
->join("\n- ");

PHPUnit::assertSame(
0, $count,
"{$count} unexpected events were dispatched:\n\n- $eventNames\n"
);
}








public function dispatched($event, $callback = null)
{
if (! $this->hasDispatched($event)) {
return new Collection;
}

$callback = $callback ?: fn () => true;

return (new Collection($this->events[$event]))->filter(
fn ($arguments) => $callback(...$arguments)
);
}







public function hasDispatched($event)
{
return isset($this->events[$event]) && ! empty($this->events[$event]);
}








public function listen($events, $listener = null)
{
$this->dispatcher->listen($events, $listener);
}







public function hasListeners($eventName)
{
return $this->dispatcher->hasListeners($eventName);
}








public function push($event, $payload = [])
{

}







public function subscribe($subscriber)
{
$this->dispatcher->subscribe($subscriber);
}







public function flush($event)
{

}









public function dispatch($event, $payload = [], $halt = false)
{
$name = is_object($event) ? get_class($event) : (string) $event;

if ($this->shouldFakeEvent($name, $payload)) {
$this->fakeEvent($event, $name, func_get_args());
} else {
return $this->dispatcher->dispatch($event, $payload, $halt);
}
}








protected function shouldFakeEvent($eventName, $payload)
{
if ($this->shouldDispatchEvent($eventName, $payload)) {
return false;
}

if (empty($this->eventsToFake)) {
return true;
}

return (new Collection($this->eventsToFake))
->filter(function ($event) use ($eventName, $payload) {
return $event instanceof Closure
? $event($eventName, $payload)
: $event === $eventName;
})
->isNotEmpty();
}









protected function fakeEvent($event, $name, $arguments)
{
if ($event instanceof ShouldDispatchAfterCommit && Container::getInstance()->bound('db.transactions')) {
return Container::getInstance()->make('db.transactions')
->addCallback(fn () => $this->events[$name][] = $arguments);
}

$this->events[$name][] = $arguments;
}








protected function shouldDispatchEvent($eventName, $payload)
{
if (empty($this->eventsToDispatch)) {
return false;
}

return (new Collection($this->eventsToDispatch))
->filter(function ($event) use ($eventName, $payload) {
return $event instanceof Closure
? $event($eventName, $payload)
: $event === $eventName;
})
->isNotEmpty();
}







public function forget($event)
{

}






public function forgetPushed()
{

}








public function until($event, $payload = [])
{
return $this->dispatch($event, $payload, true);
}






public function dispatchedEvents()
{
return $this->events;
}








public function __call($method, $parameters)
{
return $this->forwardCallTo($this->dispatcher, $method, $parameters);
}
}
