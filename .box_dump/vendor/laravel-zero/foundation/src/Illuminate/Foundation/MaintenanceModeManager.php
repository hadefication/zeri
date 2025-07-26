<?php

namespace Illuminate\Foundation;

use Illuminate\Support\Manager;

class MaintenanceModeManager extends Manager
{





protected function createFileDriver(): FileBasedMaintenanceMode
{
return new FileBasedMaintenanceMode();
}








protected function createCacheDriver(): CacheBasedMaintenanceMode
{
return new CacheBasedMaintenanceMode(
$this->container->make('cache'),
$this->config->get('app.maintenance.store') ?: $this->config->get('cache.default'),
'illuminate:foundation:down'
);
}






public function getDefaultDriver(): string
{
return $this->config->get('app.maintenance.driver', 'file');
}
}
