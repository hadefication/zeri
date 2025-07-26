<?php

namespace Illuminate\Foundation\Testing\Concerns;

use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Testing\Constraints\CountInDatabase;
use Illuminate\Testing\Constraints\HasInDatabase;
use Illuminate\Testing\Constraints\NotSoftDeletedInDatabase;
use Illuminate\Testing\Constraints\SoftDeletedInDatabase;
use PHPUnit\Framework\Constraint\LogicalNot as ReverseConstraint;

trait InteractsWithDatabase
{








protected function assertDatabaseHas($table, array $data = [], $connection = null)
{
if ($table instanceof Model) {
$data = [
$table->getKeyName() => $table->getKey(),
...$data,
];
}

$this->assertThat(
$this->getTable($table), new HasInDatabase($this->getConnection($connection, $table), $data)
);

return $this;
}









protected function assertDatabaseMissing($table, array $data = [], $connection = null)
{
if ($table instanceof Model) {
$data = [
$table->getKeyName() => $table->getKey(),
...$data,
];
}

$constraint = new ReverseConstraint(
new HasInDatabase($this->getConnection($connection, $table), $data)
);

$this->assertThat($this->getTable($table), $constraint);

return $this;
}









protected function assertDatabaseCount($table, int $count, $connection = null)
{
$this->assertThat(
$this->getTable($table), new CountInDatabase($this->getConnection($connection, $table), $count)
);

return $this;
}








protected function assertDatabaseEmpty($table, $connection = null)
{
$this->assertThat(
$this->getTable($table), new CountInDatabase($this->getConnection($connection, $table), 0)
);

return $this;
}










protected function assertSoftDeleted($table, array $data = [], $connection = null, $deletedAtColumn = 'deleted_at')
{
if ($this->isSoftDeletableModel($table)) {
return $this->assertSoftDeleted(
$table->getTable(),
array_merge($data, [$table->getKeyName() => $table->getKey()]),
$table->getConnectionName(),
$table->getDeletedAtColumn()
);
}

$this->assertThat(
$this->getTable($table),
new SoftDeletedInDatabase(
$this->getConnection($connection, $table),
$data,
$this->getDeletedAtColumn($table, $deletedAtColumn)
)
);

return $this;
}










protected function assertNotSoftDeleted($table, array $data = [], $connection = null, $deletedAtColumn = 'deleted_at')
{
if ($this->isSoftDeletableModel($table)) {
return $this->assertNotSoftDeleted(
$table->getTable(),
array_merge($data, [$table->getKeyName() => $table->getKey()]),
$table->getConnectionName(),
$table->getDeletedAtColumn()
);
}

$this->assertThat(
$this->getTable($table),
new NotSoftDeletedInDatabase(
$this->getConnection($connection, $table),
$data,
$this->getDeletedAtColumn($table, $deletedAtColumn)
)
);

return $this;
}







protected function assertModelExists($model)
{
return $this->assertDatabaseHas($model);
}







protected function assertModelMissing($model)
{
return $this->assertDatabaseMissing($model);
}








public function expectsDatabaseQueryCount($expected, $connection = null)
{
with($this->getConnection($connection), function ($connectionInstance) use ($expected, $connection) {
$actual = 0;

$connectionInstance->listen(function (QueryExecuted $event) use (&$actual, $connectionInstance, $connection) {
if (is_null($connection) || $connectionInstance === $event->connection) {
$actual++;
}
});

$this->beforeApplicationDestroyed(function () use (&$actual, $expected, $connectionInstance) {
$this->assertSame(
$expected,
$actual,
"Expected {$expected} database queries on the [{$connectionInstance->getName()}] connection. {$actual} occurred."
);
});
});

return $this;
}







protected function isSoftDeletableModel($model)
{
return $model instanceof Model
&& in_array(SoftDeletes::class, class_uses_recursive($model));
}








public function castAsJson($value, $connection = null)
{
if ($value instanceof Jsonable) {
$value = $value->toJson();
} elseif (is_array($value) || is_object($value)) {
$value = json_encode($value);
}

$db = DB::connection($connection);

$value = $db->getPdo()->quote($value);

return $db->raw(
$db->getQueryGrammar()->compileJsonValueCast($value)
);
}








protected function getConnection($connection = null, $table = null)
{
$database = $this->app->make('db');

$connection = $connection ?: $this->getTableConnection($table) ?: $database->getDefaultConnection();

return $database->connection($connection);
}







protected function getTable($table)
{
if ($table instanceof Model) {
return $table->getTable();
}

return $this->newModelFor($table)?->getTable() ?: $table;
}







protected function getTableConnection($table)
{
if ($table instanceof Model) {
return $table->getConnectionName();
}

return $this->newModelFor($table)?->getConnectionName();
}








protected function getDeletedAtColumn($table, $defaultColumnName = 'deleted_at')
{
return $this->newModelFor($table)?->getDeletedAtColumn() ?: $defaultColumnName;
}







protected function newModelFor($table)
{
return is_subclass_of($table, Model::class) ? (new $table) : null;
}







public function seed($class = 'Database\\Seeders\\DatabaseSeeder')
{
foreach (Arr::wrap($class) as $class) {
$this->artisan('db:seed', ['--class' => $class, '--no-interaction' => true]);
}

return $this;
}
}
