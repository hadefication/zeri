<?php

namespace Illuminate\Foundation\Testing;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Testing\Traits\CanConfigureMigrationCommands;

trait DatabaseMigrations
{
use CanConfigureMigrationCommands;






public function runDatabaseMigrations()
{
$this->beforeRefreshingDatabase();
$this->refreshTestDatabase();
$this->afterRefreshingDatabase();

$this->beforeApplicationDestroyed(function () {
$this->artisan('migrate:rollback');

RefreshDatabaseState::$migrated = false;
});
}






protected function refreshTestDatabase()
{
$this->artisan('migrate:fresh', $this->migrateFreshUsing());

$this->app[Kernel::class]->setArtisan(null);
}






protected function beforeRefreshingDatabase()
{

}






protected function afterRefreshingDatabase()
{

}
}
