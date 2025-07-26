<?php

namespace Illuminate\Cache;

use Illuminate\Contracts\Cache\LockProvider;
use Illuminate\Support\Carbon;
use Illuminate\Support\InteractsWithTime;

class ArrayStore extends TaggableStore implements LockProvider
{
use InteractsWithTime, RetrievesMultipleKeys;






protected $storage = [];






public $locks = [];






protected $serializesValues;






public function __construct($serializesValues = false)
{
$this->serializesValues = $serializesValues;
}







public function get($key)
{
if (! isset($this->storage[$key])) {
return;
}

$item = $this->storage[$key];

$expiresAt = $item['expiresAt'] ?? 0;

if ($expiresAt !== 0 && (Carbon::now()->getPreciseTimestamp(3) / 1000) >= $expiresAt) {
$this->forget($key);

return;
}

return $this->serializesValues ? unserialize($item['value']) : $item['value'];
}









public function put($key, $value, $seconds)
{
$this->storage[$key] = [
'value' => $this->serializesValues ? serialize($value) : $value,
'expiresAt' => $this->calculateExpiration($seconds),
];

return true;
}








public function increment($key, $value = 1)
{
if (! is_null($existing = $this->get($key))) {
return tap(((int) $existing) + $value, function ($incremented) use ($key) {
$value = $this->serializesValues ? serialize($incremented) : $incremented;

$this->storage[$key]['value'] = $value;
});
}

$this->forever($key, $value);

return $value;
}








public function decrement($key, $value = 1)
{
return $this->increment($key, $value * -1);
}








public function forever($key, $value)
{
return $this->put($key, $value, 0);
}







public function forget($key)
{
if (array_key_exists($key, $this->storage)) {
unset($this->storage[$key]);

return true;
}

return false;
}






public function flush()
{
$this->storage = [];

return true;
}






public function getPrefix()
{
return '';
}







protected function calculateExpiration($seconds)
{
return $this->toTimestamp($seconds);
}







protected function toTimestamp($seconds)
{
return $seconds > 0 ? (Carbon::now()->getPreciseTimestamp(3) / 1000) + $seconds : 0;
}









public function lock($name, $seconds = 0, $owner = null)
{
return new ArrayLock($this, $name, $seconds, $owner);
}








public function restoreLock($name, $owner)
{
return $this->lock($name, 0, $owner);
}
}
