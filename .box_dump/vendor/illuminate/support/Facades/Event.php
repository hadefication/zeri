<?php

namespace Illuminate\Support\Facades;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Testing\Fakes\EventFake;



































class Event extends Facade
{






public static function fake($eventsToFake = [])
{
$actualDispatcher = static::isFake()
? static::getFacadeRoot()->dispatcher
: static::getFacadeRoot();

return tap(new EventFake($actualDispatcher, $eventsToFake), function ($fake) {
static::swap($fake);

Model::setEventDispatcher($fake);
Cache::refreshEventDispatcher();
});
}







public static function fakeExcept($eventsToAllow)
{
return static::fake([
function ($eventName) use ($eventsToAllow) {
return ! in_array($eventName, (array) $eventsToAllow);
},
]);
}








public static function fakeFor(callable $callable, array $eventsToFake = [])
{
$originalDispatcher = static::getFacadeRoot();

static::fake($eventsToFake);

try {
return $callable();
} finally {
static::swap($originalDispatcher);

Model::setEventDispatcher($originalDispatcher);
Cache::refreshEventDispatcher();
}
}








public static function fakeExceptFor(callable $callable, array $eventsToAllow = [])
{
$originalDispatcher = static::getFacadeRoot();

static::fakeExcept($eventsToAllow);

try {
return $callable();
} finally {
static::swap($originalDispatcher);

Model::setEventDispatcher($originalDispatcher);
Cache::refreshEventDispatcher();
}
}






protected static function getFacadeAccessor()
{
return 'events';
}
}
