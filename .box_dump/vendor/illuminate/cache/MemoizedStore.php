<?php

namespace Illuminate\Cache;

use BadMethodCallException;
use Illuminate\Contracts\Cache\LockProvider;
use Illuminate\Contracts\Cache\Store;

class MemoizedStore implements LockProvider, Store
{





protected $cache = [];







public function __construct(
protected $name,
protected $repository,
) {

}







public function get($key)
{
$prefixedKey = $this->prefix($key);

if (array_key_exists($prefixedKey, $this->cache)) {
return $this->cache[$prefixedKey];
}

return $this->cache[$prefixedKey] = $this->repository->get($key);
}








public function many(array $keys)
{
[$memoized, $retrieved, $missing] = [[], [], []];

foreach ($keys as $key) {
$prefixedKey = $this->prefix($key);

if (array_key_exists($prefixedKey, $this->cache)) {
$memoized[$key] = $this->cache[$prefixedKey];
} else {
$missing[] = $key;
}
}

if (count($missing) > 0) {
$retrieved = tap($this->repository->many($missing), function ($values) {
$this->cache = [
...$this->cache,
...collect($values)->mapWithKeys(fn ($value, $key) => [
$this->prefix($key) => $value,
]),
];
});
}

$result = [];

foreach ($keys as $key) {
if (array_key_exists($key, $memoized)) {
$result[$key] = $memoized[$key];
} else {
$result[$key] = $retrieved[$key];
}
}

return $result;
}









public function put($key, $value, $seconds)
{
unset($this->cache[$this->prefix($key)]);

return $this->repository->put($key, $value, $seconds);
}








public function putMany(array $values, $seconds)
{
foreach ($values as $key => $value) {
unset($this->cache[$this->prefix($key)]);
}

return $this->repository->putMany($values, $seconds);
}








public function increment($key, $value = 1)
{
unset($this->cache[$this->prefix($key)]);

return $this->repository->increment($key, $value);
}








public function decrement($key, $value = 1)
{
unset($this->cache[$this->prefix($key)]);

return $this->repository->decrement($key, $value);
}








public function forever($key, $value)
{
unset($this->cache[$this->prefix($key)]);

return $this->repository->forever($key, $value);
}









public function lock($name, $seconds = 0, $owner = null)
{
if (! $this->repository->getStore() instanceof LockProvider) {
throw new BadMethodCallException('This cache store does not support locks.');
}

return $this->repository->getStore()->lock(...func_get_args());
}








public function restoreLock($name, $owner)
{
if (! $this->repository instanceof LockProvider) {
throw new BadMethodCallException('This cache store does not support locks.');
}

return $this->repository->resoreLock(...func_get_args());
}







public function forget($key)
{
unset($this->cache[$this->prefix($key)]);

return $this->repository->forget($key);
}






public function flush()
{
$this->cache = [];

return $this->repository->flush();
}






public function getPrefix()
{
return $this->repository->getPrefix();
}







protected function prefix($key)
{
return $this->getPrefix().$key;
}
}
