<?php

namespace Illuminate\Support\Facades;



















class RateLimiter extends Facade
{





protected static function getFacadeAccessor()
{
return \Illuminate\Cache\RateLimiter::class;
}
}
