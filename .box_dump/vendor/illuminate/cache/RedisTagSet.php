<?php

namespace Illuminate\Cache;

use Illuminate\Redis\Connections\PhpRedisConnection;
use Illuminate\Support\Carbon;
use Illuminate\Support\LazyCollection;

class RedisTagSet extends TagSet
{








public function addEntry(string $key, ?int $ttl = null, $updateWhen = null)
{
$ttl = is_null($ttl) ? -1 : Carbon::now()->addSeconds($ttl)->getTimestamp();

foreach ($this->tagIds() as $tagKey) {
if ($updateWhen) {
$this->store->connection()->zadd($this->store->getPrefix().$tagKey, $updateWhen, $ttl, $key);
} else {
$this->store->connection()->zadd($this->store->getPrefix().$tagKey, $ttl, $key);
}
}
}






public function entries()
{
$connection = $this->store->connection();

$defaultCursorValue = match (true) {
$connection instanceof PhpRedisConnection && version_compare(phpversion('redis'), '6.1.0', '>=') => null,
default => '0',
};

return new LazyCollection(function () use ($connection, $defaultCursorValue) {
foreach ($this->tagIds() as $tagKey) {
$cursor = $defaultCursorValue;

do {
[$cursor, $entries] = $connection->zscan(
$this->store->getPrefix().$tagKey,
$cursor,
['match' => '*', 'count' => 1000]
);

if (! is_array($entries)) {
break;
}

$entries = array_unique(array_keys($entries));

if (count($entries) === 0) {
continue;
}

foreach ($entries as $entry) {
yield $entry;
}
} while (((string) $cursor) !== $defaultCursorValue);
}
});
}






public function flushStaleEntries()
{
$this->store->connection()->pipeline(function ($pipe) {
foreach ($this->tagIds() as $tagKey) {
$pipe->zremrangebyscore($this->store->getPrefix().$tagKey, 0, Carbon::now()->getTimestamp());
}
});
}







public function flushTag($name)
{
return $this->resetTag($name);
}







public function resetTag($name)
{
$this->store->forget($this->tagKey($name));

return $this->tagId($name);
}







public function tagId($name)
{
return "tag:{$name}:entries";
}







public function tagKey($name)
{
return "tag:{$name}:entries";
}
}
