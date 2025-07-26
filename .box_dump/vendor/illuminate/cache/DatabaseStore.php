<?php

namespace Illuminate\Cache;

use Closure;
use Illuminate\Contracts\Cache\LockProvider;
use Illuminate\Contracts\Cache\Store;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\PostgresConnection;
use Illuminate\Database\QueryException;
use Illuminate\Database\SQLiteConnection;
use Illuminate\Database\SqlServerConnection;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\InteractsWithTime;
use Illuminate\Support\Str;

class DatabaseStore implements LockProvider, Store
{
use InteractsWithTime;






protected $connection;






protected $lockConnection;






protected $table;






protected $prefix;






protected $lockTable;






protected $lockLottery;






protected $defaultLockTimeoutInSeconds;











public function __construct(
ConnectionInterface $connection,
$table,
$prefix = '',
$lockTable = 'cache_locks',
$lockLottery = [2, 100],
$defaultLockTimeoutInSeconds = 86400,
) {
$this->table = $table;
$this->prefix = $prefix;
$this->connection = $connection;
$this->lockTable = $lockTable;
$this->lockLottery = $lockLottery;
$this->defaultLockTimeoutInSeconds = $defaultLockTimeoutInSeconds;
}







public function get($key)
{
return $this->many([$key])[$key];
}








public function many(array $keys)
{
if (count($keys) === 0) {
return [];
}

$results = array_fill_keys($keys, null);




$values = $this->table()
->whereIn('key', array_map(function ($key) {
return $this->prefix.$key;
}, $keys))
->get()
->map(function ($value) {
return is_array($value) ? (object) $value : $value;
});

$currentTime = $this->currentTime();




[$values, $expired] = $values->partition(function ($cache) use ($currentTime) {
return $cache->expiration > $currentTime;
});

if ($expired->isNotEmpty()) {
$this->forgetManyIfExpired($expired->pluck('key')->all(), prefixed: true);
}

return Arr::map($results, function ($value, $key) use ($values) {
if ($cache = $values->firstWhere('key', $this->prefix.$key)) {
return $this->unserialize($cache->value);
}

return $value;
});
}









public function put($key, $value, $seconds)
{
return $this->putMany([$key => $value], $seconds);
}








public function putMany(array $values, $seconds)
{
$serializedValues = [];

$expiration = $this->getTime() + $seconds;

foreach ($values as $key => $value) {
$serializedValues[] = [
'key' => $this->prefix.$key,
'value' => $this->serialize($value),
'expiration' => $expiration,
];
}

return $this->table()->upsert($serializedValues, 'key') > 0;
}









public function add($key, $value, $seconds)
{
if (! is_null($this->get($key))) {
return false;
}

$key = $this->prefix.$key;
$value = $this->serialize($value);
$expiration = $this->getTime() + $seconds;

if (! $this->getConnection() instanceof SqlServerConnection) {
return $this->table()->insertOrIgnore(compact('key', 'value', 'expiration')) > 0;
}

try {
return $this->table()->insert(compact('key', 'value', 'expiration'));
} catch (QueryException) {

}

return false;
}








public function increment($key, $value = 1)
{
return $this->incrementOrDecrement($key, $value, function ($current, $value) {
return $current + $value;
});
}








public function decrement($key, $value = 1)
{
return $this->incrementOrDecrement($key, $value, function ($current, $value) {
return $current - $value;
});
}









protected function incrementOrDecrement($key, $value, Closure $callback)
{
return $this->connection->transaction(function () use ($key, $value, $callback) {
$prefixed = $this->prefix.$key;

$cache = $this->table()->where('key', $prefixed)
->lockForUpdate()->first();




if (is_null($cache)) {
return false;
}

$cache = is_array($cache) ? (object) $cache : $cache;

$current = $this->unserialize($cache->value);




$new = $callback((int) $current, $value);

if (! is_numeric($current)) {
return false;
}




$this->table()->where('key', $prefixed)->update([
'value' => $this->serialize($new),
]);

return $new;
});
}






protected function getTime()
{
return $this->currentTime();
}








public function forever($key, $value)
{
return $this->put($key, $value, 315360000);
}









public function lock($name, $seconds = 0, $owner = null)
{
return new DatabaseLock(
$this->lockConnection ?? $this->connection,
$this->lockTable,
$this->prefix.$name,
$seconds,
$owner,
$this->lockLottery,
$this->defaultLockTimeoutInSeconds
);
}








public function restoreLock($name, $owner)
{
return $this->lock($name, 0, $owner);
}







public function forget($key)
{
return $this->forgetMany([$key]);
}







public function forgetIfExpired($key)
{
return $this->forgetManyIfExpired([$key]);
}







protected function forgetMany(array $keys)
{
$this->table()->whereIn('key', (new Collection($keys))->flatMap(fn ($key) => [
$this->prefix.$key,
"{$this->prefix}illuminate:cache:flexible:created:{$key}",
])->all())->delete();

return true;
}








protected function forgetManyIfExpired(array $keys, bool $prefixed = false)
{
$this->table()
->whereIn('key', (new Collection($keys))->flatMap(fn ($key) => $prefixed ? [
$key,
$this->prefix.'illuminate:cache:flexible:created:'.Str::chopStart($key, $this->prefix),
] : [
"{$this->prefix}{$key}",
"{$this->prefix}illuminate:cache:flexible:created:{$key}",
])->all())
->where('expiration', '<=', $this->getTime())
->delete();

return true;
}






public function flush()
{
$this->table()->delete();

return true;
}






protected function table()
{
return $this->connection->table($this->table);
}






public function getConnection()
{
return $this->connection;
}







public function setLockConnection($connection)
{
$this->lockConnection = $connection;

return $this;
}






public function getPrefix()
{
return $this->prefix;
}







public function setPrefix($prefix)
{
$this->prefix = $prefix;
}







protected function serialize($value)
{
$result = serialize($value);

if (($this->connection instanceof PostgresConnection ||
$this->connection instanceof SQLiteConnection) &&
str_contains($result, "\0")) {
$result = base64_encode($result);
}

return $result;
}







protected function unserialize($value)
{
if (($this->connection instanceof PostgresConnection ||
$this->connection instanceof SQLiteConnection) &&
! Str::contains($value, [':', ';'])) {
$value = base64_decode($value);
}

return unserialize($value);
}
}
