<?php

namespace Illuminate\Cache;

use Illuminate\Cache\Events\CacheFlushed;
use Illuminate\Cache\Events\CacheFlushing;

class RedisTaggedCache extends TaggedCache
{








public function add($key, $value, $ttl = null)
{
$seconds = null;

if ($ttl !== null) {
$seconds = $this->getSeconds($ttl);

if ($seconds > 0) {
$this->tags->addEntry(
$this->itemKey($key),
$seconds
);
}
}

return parent::add($key, $value, $ttl);
}









public function put($key, $value, $ttl = null)
{
if (is_null($ttl)) {
return $this->forever($key, $value);
}

$seconds = $this->getSeconds($ttl);

if ($seconds > 0) {
$this->tags->addEntry(
$this->itemKey($key),
$seconds
);
}

return parent::put($key, $value, $ttl);
}








public function increment($key, $value = 1)
{
$this->tags->addEntry($this->itemKey($key), updateWhen: 'NX');

return parent::increment($key, $value);
}








public function decrement($key, $value = 1)
{
$this->tags->addEntry($this->itemKey($key), updateWhen: 'NX');

return parent::decrement($key, $value);
}








public function forever($key, $value)
{
$this->tags->addEntry($this->itemKey($key));

return parent::forever($key, $value);
}






public function flush()
{
$this->event(new CacheFlushing($this->getName()));

$this->flushValues();
$this->tags->flush();

$this->event(new CacheFlushed($this->getName()));

return true;
}






protected function flushValues()
{
$entries = $this->tags->entries()
->map(fn (string $key) => $this->store->getPrefix().$key)
->chunk(1000);

foreach ($entries as $cacheKeys) {
$this->store->connection()->del(...$cacheKeys);
}
}






public function flushStale()
{
$this->tags->flushStaleEntries();

return true;
}
}
