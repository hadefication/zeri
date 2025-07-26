<?php

namespace Illuminate\Cache;

use Illuminate\Contracts\Cache\LockProvider;
use Illuminate\Contracts\Redis\Factory as Redis;
use Illuminate\Redis\Connections\PhpRedisClusterConnection;
use Illuminate\Redis\Connections\PhpRedisConnection;
use Illuminate\Redis\Connections\PredisClusterConnection;
use Illuminate\Redis\Connections\PredisConnection;
use Illuminate\Support\LazyCollection;
use Illuminate\Support\Str;

class RedisStore extends TaggableStore implements LockProvider
{
use RetrievesMultipleKeys {
putMany as private putManyAlias;
}






protected $redis;






protected $prefix;






protected $connection;






protected $lockConnection;








public function __construct(Redis $redis, $prefix = '', $connection = 'default')
{
$this->redis = $redis;
$this->setPrefix($prefix);
$this->setConnection($connection);
}







public function get($key)
{
$connection = $this->connection();

$value = $connection->get($this->prefix.$key);

return ! is_null($value) ? $this->connectionAwareUnserialize($value, $connection) : null;
}









public function many(array $keys)
{
if (count($keys) === 0) {
return [];
}

$results = [];

$connection = $this->connection();

$values = $connection->mget(array_map(function ($key) {
return $this->prefix.$key;
}, $keys));

foreach ($values as $index => $value) {
$results[$keys[$index]] = ! is_null($value) ? $this->connectionAwareUnserialize($value, $connection) : null;
}

return $results;
}









public function put($key, $value, $seconds)
{
$connection = $this->connection();

return (bool) $connection->setex(
$this->prefix.$key, (int) max(1, $seconds), $this->connectionAwareSerialize($value, $connection)
);
}








public function putMany(array $values, $seconds)
{
$connection = $this->connection();


if ($connection instanceof PhpRedisClusterConnection ||
$connection instanceof PredisClusterConnection) {
return $this->putManyAlias($values, $seconds);
}

$serializedValues = [];

foreach ($values as $key => $value) {
$serializedValues[$this->prefix.$key] = $this->connectionAwareSerialize($value, $connection);
}

$connection->multi();

$manyResult = null;

foreach ($serializedValues as $key => $value) {
$result = (bool) $connection->setex(
$key, (int) max(1, $seconds), $value
);

$manyResult = is_null($manyResult) ? $result : $result && $manyResult;
}

$connection->exec();

return $manyResult ?: false;
}









public function add($key, $value, $seconds)
{
$connection = $this->connection();

return (bool) $connection->eval(
LuaScripts::add(), 1, $this->prefix.$key, $this->pack($value, $connection), (int) max(1, $seconds)
);
}








public function increment($key, $value = 1)
{
return $this->connection()->incrby($this->prefix.$key, $value);
}








public function decrement($key, $value = 1)
{
return $this->connection()->decrby($this->prefix.$key, $value);
}








public function forever($key, $value)
{
$connection = $this->connection();

return (bool) $connection->set($this->prefix.$key, $this->connectionAwareSerialize($value, $connection));
}









public function lock($name, $seconds = 0, $owner = null)
{
$lockName = $this->prefix.$name;

$lockConnection = $this->lockConnection();

if ($lockConnection instanceof PhpRedisConnection) {
return new PhpRedisLock($lockConnection, $lockName, $seconds, $owner);
}

return new RedisLock($lockConnection, $lockName, $seconds, $owner);
}








public function restoreLock($name, $owner)
{
return $this->lock($name, 0, $owner);
}







public function forget($key)
{
return (bool) $this->connection()->del($this->prefix.$key);
}






public function flush()
{
$this->connection()->flushdb();

return true;
}






public function flushStaleTags()
{
foreach ($this->currentTags()->chunk(1000) as $tags) {
$this->tags($tags->all())->flushStale();
}
}







public function tags($names)
{
return new RedisTaggedCache(
$this, new RedisTagSet($this, is_array($names) ? $names : func_get_args())
);
}







protected function currentTags($chunkSize = 1000)
{
$connection = $this->connection();


$connectionPrefix = match (true) {
$connection instanceof PhpRedisConnection => $connection->_prefix(''),
$connection instanceof PredisConnection => $connection->getOptions()->prefix ?: '',
default => '',
};

$defaultCursorValue = match (true) {
$connection instanceof PhpRedisConnection && version_compare(phpversion('redis'), '6.1.0', '>=') => null,
default => '0',
};

$prefix = $connectionPrefix.$this->getPrefix();

return (new LazyCollection(function () use ($connection, $chunkSize, $prefix, $defaultCursorValue) {
$cursor = $defaultCursorValue;

do {
[$cursor, $tagsChunk] = $connection->scan(
$cursor,
['match' => $prefix.'tag:*:entries', 'count' => $chunkSize]
);

if (! is_array($tagsChunk)) {
break;
}

$tagsChunk = array_unique($tagsChunk);

if (empty($tagsChunk)) {
continue;
}

foreach ($tagsChunk as $tag) {
yield $tag;
}
} while (((string) $cursor) !== $defaultCursorValue);
}))->map(fn (string $tagKey) => Str::match('/^'.preg_quote($prefix, '/').'tag:(.*):entries$/', $tagKey));
}






public function connection()
{
return $this->redis->connection($this->connection);
}






public function lockConnection()
{
return $this->redis->connection($this->lockConnection ?? $this->connection);
}







public function setConnection($connection)
{
$this->connection = $connection;
}







public function setLockConnection($connection)
{
$this->lockConnection = $connection;

return $this;
}






public function getRedis()
{
return $this->redis;
}






public function getPrefix()
{
return $this->prefix;
}







public function setPrefix($prefix)
{
$this->prefix = $prefix;
}








protected function pack($value, $connection)
{
if ($connection instanceof PhpRedisConnection) {
if ($connection->serialized()) {
return $connection->pack([$value])[0];
}

if ($connection->compressed()) {
return $connection->pack([$this->serialize($value)])[0];
}
}

return $this->serialize($value);
}







protected function serialize($value)
{
return $this->shouldBeStoredWithoutSerialization($value) ? $value : serialize($value);
}







protected function shouldBeStoredWithoutSerialization($value): bool
{
return is_numeric($value) && ! in_array($value, [INF, -INF]) && ! is_nan($value);
}







protected function unserialize($value)
{
return is_numeric($value) ? $value : unserialize($value);
}








protected function connectionAwareSerialize($value, $connection)
{
if ($connection instanceof PhpRedisConnection && $connection->serialized()) {
return $value;
}

return $this->serialize($value);
}








protected function connectionAwareUnserialize($value, $connection)
{
if ($connection instanceof PhpRedisConnection && $connection->serialized()) {
return $value;
}

return $this->unserialize($value);
}
}
