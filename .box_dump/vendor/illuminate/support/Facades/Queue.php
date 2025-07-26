<?php

namespace Illuminate\Support\Facades;

use Illuminate\Queue\Worker;
use Illuminate\Support\Testing\Fakes\QueueFake;





























































class Queue extends Facade
{







public static function popUsing($workerName, $callback)
{
Worker::popUsing($workerName, $callback);
}







public static function fake($jobsToFake = [])
{
$actualQueueManager = static::isFake()
? static::getFacadeRoot()->queue
: static::getFacadeRoot();

return tap(new QueueFake(static::getFacadeApplication(), $jobsToFake, $actualQueueManager), function ($fake) {
static::swap($fake);
});
}







public static function fakeExcept($jobsToAllow)
{
return static::fake()->except($jobsToAllow);
}








public static function fakeFor(callable $callable, array $jobsToFake = [])
{
$originalQueueManager = static::getFacadeRoot();

static::fake($jobsToFake);

try {
return $callable();
} finally {
static::swap($originalQueueManager);
}
}








public static function fakeExceptFor(callable $callable, array $jobsToAllow = [])
{
$originalQueueManager = static::getFacadeRoot();

static::fakeExcept($jobsToAllow);

try {
return $callable();
} finally {
static::swap($originalQueueManager);
}
}






protected static function getFacadeAccessor()
{
return 'queue';
}
}
