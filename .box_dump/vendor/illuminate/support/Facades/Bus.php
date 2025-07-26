<?php

namespace Illuminate\Support\Facades;

use Illuminate\Bus\BatchRepository;
use Illuminate\Contracts\Bus\Dispatcher as BusDispatcherContract;
use Illuminate\Foundation\Bus\PendingChain;
use Illuminate\Support\Testing\Fakes\BusFake;


















































class Bus extends Facade
{







public static function fake($jobsToFake = [], ?BatchRepository $batchRepository = null)
{
$actualDispatcher = static::isFake()
? static::getFacadeRoot()->dispatcher
: static::getFacadeRoot();

return tap(new BusFake($actualDispatcher, $jobsToFake, $batchRepository), function ($fake) {
static::swap($fake);
});
}







public static function dispatchChain($jobs)
{
$jobs = is_array($jobs) ? $jobs : func_get_args();

return (new PendingChain(array_shift($jobs), $jobs))
->dispatch();
}






protected static function getFacadeAccessor()
{
return BusDispatcherContract::class;
}
}
