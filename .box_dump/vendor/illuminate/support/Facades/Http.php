<?php

namespace Illuminate\Support\Facades;

use Illuminate\Http\Client\Factory;

































































































class Http extends Facade
{





protected static function getFacadeAccessor()
{
return Factory::class;
}







public static function fake($callback = null)
{
return tap(static::getFacadeRoot(), function ($fake) use ($callback) {
static::swap($fake->fake($callback));
});
}







public static function fakeSequence(string $urlPattern = '*')
{
$fake = tap(static::getFacadeRoot(), function ($fake) {
static::swap($fake);
});

return $fake->fakeSequence($urlPattern);
}







public static function preventStrayRequests($prevent = true)
{
return tap(static::getFacadeRoot(), function ($fake) use ($prevent) {
static::swap($fake->preventStrayRequests($prevent));
});
}








public static function stubUrl($url, $callback)
{
return tap(static::getFacadeRoot(), function ($fake) use ($url, $callback) {
static::swap($fake->stubUrl($url, $callback));
});
}
}
