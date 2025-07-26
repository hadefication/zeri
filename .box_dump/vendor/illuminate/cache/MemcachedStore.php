<?php

namespace Illuminate\Cache;

use Illuminate\Contracts\Cache\LockProvider;
use Illuminate\Support\InteractsWithTime;
use Memcached;
use ReflectionMethod;

class MemcachedStore extends TaggableStore implements LockProvider
{
use InteractsWithTime;






protected $memcached;






protected $prefix;






protected $onVersionThree;







public function __construct($memcached, $prefix = '')
{
$this->setPrefix($prefix);
$this->memcached = $memcached;

$this->onVersionThree = (new ReflectionMethod('Memcached', 'getMulti'))
->getNumberOfParameters() == 2;
}







public function get($key)
{
$value = $this->memcached->get($this->prefix.$key);

if ($this->memcached->getResultCode() == 0) {
return $value;
}
}









public function many(array $keys)
{
$prefixedKeys = array_map(function ($key) {
return $this->prefix.$key;
}, $keys);

if ($this->onVersionThree) {
$values = $this->memcached->getMulti($prefixedKeys, Memcached::GET_PRESERVE_ORDER);
} else {
$null = null;

$values = $this->memcached->getMulti($prefixedKeys, $null, Memcached::GET_PRESERVE_ORDER);
}

if ($this->memcached->getResultCode() != 0) {
return array_fill_keys($keys, null);
}

return array_combine($keys, $values);
}









public function put($key, $value, $seconds)
{
return $this->memcached->set(
$this->prefix.$key, $value, $this->calculateExpiration($seconds)
);
}








public function putMany(array $values, $seconds)
{
$prefixedValues = [];

foreach ($values as $key => $value) {
$prefixedValues[$this->prefix.$key] = $value;
}

return $this->memcached->setMulti(
$prefixedValues, $this->calculateExpiration($seconds)
);
}









public function add($key, $value, $seconds)
{
return $this->memcached->add(
$this->prefix.$key, $value, $this->calculateExpiration($seconds)
);
}








public function increment($key, $value = 1)
{
return $this->memcached->increment($this->prefix.$key, $value);
}








public function decrement($key, $value = 1)
{
return $this->memcached->decrement($this->prefix.$key, $value);
}








public function forever($key, $value)
{
return $this->put($key, $value, 0);
}









public function lock($name, $seconds = 0, $owner = null)
{
return new MemcachedLock($this->memcached, $this->prefix.$name, $seconds, $owner);
}








public function restoreLock($name, $owner)
{
return $this->lock($name, 0, $owner);
}







public function forget($key)
{
return $this->memcached->delete($this->prefix.$key);
}






public function flush()
{
return $this->memcached->flush();
}







protected function calculateExpiration($seconds)
{
return $this->toTimestamp($seconds);
}







protected function toTimestamp($seconds)
{
return $seconds > 0 ? $this->availableAt($seconds) : 0;
}






public function getMemcached()
{
return $this->memcached;
}






public function getPrefix()
{
return $this->prefix;
}







public function setPrefix($prefix)
{
$this->prefix = $prefix;
}
}
