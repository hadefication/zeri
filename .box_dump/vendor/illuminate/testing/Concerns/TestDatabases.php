<?php

namespace Illuminate\Testing\Concerns;

use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\ParallelTesting;
use Illuminate\Support\Facades\Schema;

trait TestDatabases
{





protected static $schemaIsUpToDate = false;






protected function bootTestDatabase()
{
ParallelTesting::setUpProcess(function () {
$this->whenNotUsingInMemoryDatabase(function ($database) {
if (ParallelTesting::option('recreate_databases')) {
Schema::dropDatabaseIfExists(
$this->testDatabase($database)
);
}
});
});

ParallelTesting::setUpTestCase(function ($testCase) {
$uses = array_flip(class_uses_recursive(get_class($testCase)));

$databaseTraits = [
Testing\DatabaseMigrations::class,
Testing\DatabaseTransactions::class,
Testing\DatabaseTruncation::class,
Testing\RefreshDatabase::class,
];

if (Arr::hasAny($uses, $databaseTraits) && ! ParallelTesting::option('without_databases')) {
$this->whenNotUsingInMemoryDatabase(function ($database) use ($uses) {
[$testDatabase, $created] = $this->ensureTestDatabaseExists($database);

$this->switchToDatabase($testDatabase);

if (isset($uses[Testing\DatabaseTransactions::class])) {
$this->ensureSchemaIsUpToDate();
}

if ($created) {
ParallelTesting::callSetUpTestDatabaseCallbacks($testDatabase);
}
});
}
});

ParallelTesting::tearDownProcess(function () {
$this->whenNotUsingInMemoryDatabase(function ($database) {
if (ParallelTesting::option('drop_databases')) {
Schema::dropDatabaseIfExists(
$this->testDatabase($database)
);
}
});
});
}







protected function ensureTestDatabaseExists($database)
{
$testDatabase = $this->testDatabase($database);

try {
$this->usingDatabase($testDatabase, function () {
Schema::hasTable('dummy');
});
} catch (QueryException) {
$this->usingDatabase($database, function () use ($testDatabase) {
Schema::dropDatabaseIfExists($testDatabase);
Schema::createDatabase($testDatabase);
});

return [$testDatabase, true];
}

return [$testDatabase, false];
}






protected function ensureSchemaIsUpToDate()
{
if (! static::$schemaIsUpToDate) {
Artisan::call('migrate');

static::$schemaIsUpToDate = true;
}
}








protected function usingDatabase($database, $callable)
{
$original = DB::getConfig('database');

try {
$this->switchToDatabase($database);
$callable();
} finally {
$this->switchToDatabase($original);
}
}







protected function whenNotUsingInMemoryDatabase($callback)
{
if (ParallelTesting::option('without_databases')) {
return;
}

$database = DB::getConfig('database');

if ($database !== ':memory:') {
$callback($database);
}
}







protected function switchToDatabase($database)
{
DB::purge();

$default = config('database.default');

$url = config("database.connections.{$default}.url");

if ($url) {
config()->set(
"database.connections.{$default}.url",
preg_replace('/^(.*)(\/[\w-]*)(\??.*)$/', "$1/{$database}$3", $url),
);
} else {
config()->set(
"database.connections.{$default}.database",
$database,
);
}
}






protected function testDatabase($database)
{
$token = ParallelTesting::token();

return "{$database}_test_{$token}";
}
}
