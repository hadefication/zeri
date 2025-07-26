<?php

namespace Illuminate\Support\Facades;

use Closure;
use Illuminate\Process\Factory;












































class Process extends Facade
{





protected static function getFacadeAccessor()
{
return Factory::class;
}







public static function fake(Closure|array|null $callback = null)
{
return tap(static::getFacadeRoot(), function ($fake) use ($callback) {
static::swap($fake->fake($callback));
});
}
}
