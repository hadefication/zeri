<?php

namespace Illuminate\Support\Facades;

use Illuminate\Foundation\MaintenanceModeManager;

class MaintenanceMode extends Facade
{





protected static function getFacadeAccessor()
{
return MaintenanceModeManager::class;
}
}
