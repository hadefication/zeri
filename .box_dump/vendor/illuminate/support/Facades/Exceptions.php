<?php

namespace Illuminate\Support\Facades;

use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Support\Arr;
use Illuminate\Support\Testing\Fakes\ExceptionHandlerFake;

































class Exceptions extends Facade
{






public static function fake(array|string $exceptions = [])
{
$exceptionHandler = static::isFake()
? static::getFacadeRoot()->handler()
: static::getFacadeRoot();

return tap(new ExceptionHandlerFake($exceptionHandler, Arr::wrap($exceptions)), function ($fake) {
static::swap($fake);
});
}






protected static function getFacadeAccessor()
{
return ExceptionHandler::class;
}
}
