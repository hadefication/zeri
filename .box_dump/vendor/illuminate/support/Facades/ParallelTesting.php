<?php

namespace Illuminate\Support\Facades;



















class ParallelTesting extends Facade
{





protected static function getFacadeAccessor()
{
return \Illuminate\Testing\ParallelTesting::class;
}
}
