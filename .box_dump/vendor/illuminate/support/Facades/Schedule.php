<?php

namespace Illuminate\Support\Facades;

use Illuminate\Console\Scheduling\Schedule as ConsoleSchedule;


















































































class Schedule extends Facade
{





protected static function getFacadeAccessor()
{
return ConsoleSchedule::class;
}
}
