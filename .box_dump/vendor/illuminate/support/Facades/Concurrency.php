<?php

namespace Illuminate\Support\Facades;

use Illuminate\Concurrency\ConcurrencyManager;



















class Concurrency extends Facade
{





protected static function getFacadeAccessor()
{
return ConcurrencyManager::class;
}
}
