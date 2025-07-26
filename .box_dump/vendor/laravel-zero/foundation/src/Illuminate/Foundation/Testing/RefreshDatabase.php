<?php

namespace Illuminate\Foundation\Testing;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Testing\Traits\CanConfigureMigrationCommands;

trait RefreshDatabase
{
use CanConfigureMigrationCommands;






public function refreshDatabase()
{
$this->beforeRefreshingDatabase();

if ($this->usingInMemoryDatabases()) {
$this->restoreInMemoryDatabase();
}

$this->refreshTestDatabase();

$this->afterRefreshingDatabase();
}






protected function usingInMemoryDatabases()
{
foreach ($this->connectionsToTransact() as $name) {
if ($this->usingInMemoryDatabase($name)) {
return true;
}
}

return false;
}






protected function usingInMemoryDatabase(?string $name = null)
{
if (is_null($name)) {
$name = config('database.default');
}

return config("database.connections.{$name}.database") === ':memory:';
}






protected function restoreInMemoryDatabase()
{
$database = $this->app->make('db');

foreach ($this->connectionsToTransact() as $name) {
if (isset(RefreshDatabaseState::$inMemoryConnections[$name])) {
$database->connection($name)->setPdo(RefreshDatabaseState::$inMemoryConnections[$name]);
}
}
}






protected function refreshTestDatabase()
{
if (! RefreshDatabaseState::$migrated) {
$this->migrateDatabases();

$this->app[Kernel::class]->setArtisan(null);

RefreshDatabaseState::$migrated = true;
}

$this->beginDatabaseTransaction();
}






protected function migrateDatabases()
{
$this->artisan('migrate:fresh', $this->migrateFreshUsing());
}






public function beginDatabaseTransaction()
{
$database = $this->app->make('db');

$connections = $this->connectionsToTransact();

$this->app->instance('db.transactions', $transactionsManager = new DatabaseTransactionsManager($connections));

foreach ($connections as $name) {
$connection = $database->connection($name);

$connection->setTransactionManager($transactionsManager);

if ($this->usingInMemoryDatabase($name)) {
RefreshDatabaseState::$inMemoryConnections[$name] ??= $connection->getPdo();
}

$dispatcher = $connection->getEventDispatcher();

$connection->unsetEventDispatcher();
$connection->beginTransaction();
$connection->setEventDispatcher($dispatcher);
}

$this->beforeApplicationDestroyed(function () use ($database) {
foreach ($this->connectionsToTransact() as $name) {
$connection = $database->connection($name);
$dispatcher = $connection->getEventDispatcher();

$connection->unsetEventDispatcher();

if ($connection->getPdo() && ! $connection->getPdo()->inTransaction()) {
RefreshDatabaseState::$migrated = false;
}

$connection->rollBack();
$connection->setEventDispatcher($dispatcher);
$connection->disconnect();
}
});
}






protected function connectionsToTransact()
{
return property_exists($this, 'connectionsToTransact')
? $this->connectionsToTransact
: [config('database.default')];
}






protected function beforeRefreshingDatabase()
{

}






protected function afterRefreshingDatabase()
{

}
}
