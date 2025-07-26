<?php

namespace Illuminate\Cache;

use ArrayAccess;
use BadMethodCallException;
use Closure;
use DateTimeInterface;
use Illuminate\Cache\Events\CacheFlushed;
use Illuminate\Cache\Events\CacheFlushFailed;
use Illuminate\Cache\Events\CacheFlushing;
use Illuminate\Cache\Events\CacheHit;
use Illuminate\Cache\Events\CacheMissed;
use Illuminate\Cache\Events\ForgettingKey;
use Illuminate\Cache\Events\KeyForgetFailed;
use Illuminate\Cache\Events\KeyForgotten;
use Illuminate\Cache\Events\KeyWriteFailed;
use Illuminate\Cache\Events\KeyWritten;
use Illuminate\Cache\Events\RetrievingKey;
use Illuminate\Cache\Events\RetrievingManyKeys;
use Illuminate\Cache\Events\WritingKey;
use Illuminate\Cache\Events\WritingManyKeys;
use Illuminate\Contracts\Cache\Repository as CacheContract;
use Illuminate\Contracts\Cache\Store;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\InteractsWithTime;
use Illuminate\Support\Traits\Macroable;

use function Illuminate\Support\defer;

/**
@mixin
*/
class Repository implements ArrayAccess, CacheContract
{
use InteractsWithTime, Macroable {
__call as macroCall;
}






protected $store;






protected $events;






protected $default = 3600;






protected $config = [];







public function __construct(Store $store, array $config = [])
{
$this->store = $store;
$this->config = $config;
}







public function has($key): bool
{
return ! is_null($this->get($key));
}







public function missing($key)
{
return ! $this->has($key);
}








public function get($key, $default = null): mixed
{
if (is_array($key)) {
return $this->many($key);
}

$this->event(new RetrievingKey($this->getName(), $key));

$value = $this->store->get($this->itemKey($key));




if (is_null($value)) {
$this->event(new CacheMissed($this->getName(), $key));

$value = value($default);
} else {
$this->event(new CacheHit($this->getName(), $key, $value));
}

return $value;
}









public function many(array $keys)
{
$this->event(new RetrievingManyKeys($this->getName(), $keys));

$values = $this->store->many((new Collection($keys))
->map(fn ($value, $key) => is_string($key) ? $key : $value)
->values()
->all()
);

return (new Collection($values))
->map(fn ($value, $key) => $this->handleManyResult($keys, $key, $value))
->all();
}






public function getMultiple($keys, $default = null): iterable
{
$defaults = [];

foreach ($keys as $key) {
$defaults[$key] = $default;
}

return $this->many($defaults);
}









protected function handleManyResult($keys, $key, $value)
{



if (is_null($value)) {
$this->event(new CacheMissed($this->getName(), $key));

return (isset($keys[$key]) && ! array_is_list($keys)) ? value($keys[$key]) : null;
}




$this->event(new CacheHit($this->getName(), $key, $value));

return $value;
}








public function pull($key, $default = null)
{
return tap($this->get($key, $default), function () use ($key) {
$this->forget($key);
});
}









public function put($key, $value, $ttl = null)
{
if (is_array($key)) {
return $this->putMany($key, $value);
}

if ($ttl === null) {
return $this->forever($key, $value);
}

$seconds = $this->getSeconds($ttl);

if ($seconds <= 0) {
return $this->forget($key);
}

$this->event(new WritingKey($this->getName(), $key, $value, $seconds));

$result = $this->store->put($this->itemKey($key), $value, $seconds);

if ($result) {
$this->event(new KeyWritten($this->getName(), $key, $value, $seconds));
} else {
$this->event(new KeyWriteFailed($this->getName(), $key, $value, $seconds));
}

return $result;
}






public function set($key, $value, $ttl = null): bool
{
return $this->put($key, $value, $ttl);
}








public function putMany(array $values, $ttl = null)
{
if ($ttl === null) {
return $this->putManyForever($values);
}

$seconds = $this->getSeconds($ttl);

if ($seconds <= 0) {
return $this->deleteMultiple(array_keys($values));
}

$this->event(new WritingManyKeys($this->getName(), array_keys($values), array_values($values), $seconds));

$result = $this->store->putMany($values, $seconds);

foreach ($values as $key => $value) {
if ($result) {
$this->event(new KeyWritten($this->getName(), $key, $value, $seconds));
} else {
$this->event(new KeyWriteFailed($this->getName(), $key, $value, $seconds));
}
}

return $result;
}







protected function putManyForever(array $values)
{
$result = true;

foreach ($values as $key => $value) {
if (! $this->forever($key, $value)) {
$result = false;
}
}

return $result;
}






public function setMultiple($values, $ttl = null): bool
{
return $this->putMany(is_array($values) ? $values : iterator_to_array($values), $ttl);
}









public function add($key, $value, $ttl = null)
{
$seconds = null;

if ($ttl !== null) {
$seconds = $this->getSeconds($ttl);

if ($seconds <= 0) {
return false;
}




if (method_exists($this->store, 'add')) {
return $this->store->add(
$this->itemKey($key), $value, $seconds
);
}
}




if (is_null($this->get($key))) {
return $this->put($key, $value, $seconds);
}

return false;
}








public function increment($key, $value = 1)
{
return $this->store->increment($key, $value);
}








public function decrement($key, $value = 1)
{
return $this->store->decrement($key, $value);
}








public function forever($key, $value)
{
$this->event(new WritingKey($this->getName(), $key, $value));

$result = $this->store->forever($this->itemKey($key), $value);

if ($result) {
$this->event(new KeyWritten($this->getName(), $key, $value));
} else {
$this->event(new KeyWriteFailed($this->getName(), $key, $value));
}

return $result;
}

/**
@template







*/
public function remember($key, $ttl, Closure $callback)
{
$value = $this->get($key);




if (! is_null($value)) {
return $value;
}

$value = $callback();

$this->put($key, $value, value($ttl, $value));

return $value;
}

/**
@template






*/
public function sear($key, Closure $callback)
{
return $this->rememberForever($key, $callback);
}

/**
@template






*/
public function rememberForever($key, Closure $callback)
{
$value = $this->get($key);




if (! is_null($value)) {
return $value;
}

$this->forever($key, $value = $callback());

return $value;
}

/**
@template









*/
public function flexible($key, $ttl, $callback, $lock = null, $alwaysDefer = false)
{
[
$key => $value,
"illuminate:cache:flexible:created:{$key}" => $created,
] = $this->many([$key, "illuminate:cache:flexible:created:{$key}"]);

if (in_array(null, [$value, $created], true)) {
return tap(value($callback), fn ($value) => $this->putMany([
$key => $value,
"illuminate:cache:flexible:created:{$key}" => Carbon::now()->getTimestamp(),
], $ttl[1]));
}

if (($created + $this->getSeconds($ttl[0])) > Carbon::now()->getTimestamp()) {
return $value;
}

$refresh = function () use ($key, $ttl, $callback, $lock, $created) {
$this->store->lock(
"illuminate:cache:flexible:lock:{$key}",
$lock['seconds'] ?? 0,
$lock['owner'] ?? null,
)->get(function () use ($key, $callback, $created, $ttl) {
if ($created !== $this->get("illuminate:cache:flexible:created:{$key}")) {
return;
}

$this->putMany([
$key => value($callback),
"illuminate:cache:flexible:created:{$key}" => Carbon::now()->getTimestamp(),
], $ttl[1]);
});
};

defer($refresh, "illuminate:cache:flexible:{$key}", $alwaysDefer);

return $value;
}







public function forget($key)
{
$this->event(new ForgettingKey($this->getName(), $key));

return tap($this->store->forget($this->itemKey($key)), function ($result) use ($key) {
if ($result) {
$this->event(new KeyForgotten($this->getName(), $key));
} else {
$this->event(new KeyForgetFailed($this->getName(), $key));
}
});
}






public function delete($key): bool
{
return $this->forget($key);
}






public function deleteMultiple($keys): bool
{
$result = true;

foreach ($keys as $key) {
if (! $this->forget($key)) {
$result = false;
}
}

return $result;
}






public function clear(): bool
{
$this->event(new CacheFlushing($this->getName()));

$result = $this->store->flush();

if ($result) {
$this->event(new CacheFlushed($this->getName()));
} else {
$this->event(new CacheFlushFailed($this->getName()));
}

return $result;
}









public function tags($names)
{
if (! $this->supportsTags()) {
throw new BadMethodCallException('This cache store does not support tagging.');
}

$cache = $this->store->tags(is_array($names) ? $names : func_get_args());

$cache->config = $this->config;

if (! is_null($this->events)) {
$cache->setEventDispatcher($this->events);
}

return $cache->setDefaultCacheTime($this->default);
}







protected function itemKey($key)
{
return $key;
}







protected function getSeconds($ttl)
{
$duration = $this->parseDateInterval($ttl);

if ($duration instanceof DateTimeInterface) {
$duration = (int) ceil(
Carbon::now()->diffInMilliseconds($duration, false) / 1000
);
}

return (int) ($duration > 0 ? $duration : 0);
}






public function getName()
{
return $this->config['store'] ?? null;
}






public function supportsTags()
{
return method_exists($this->store, 'tags');
}






public function getDefaultCacheTime()
{
return $this->default;
}







public function setDefaultCacheTime($seconds)
{
$this->default = $seconds;

return $this;
}






public function getStore()
{
return $this->store;
}







public function setStore($store)
{
$this->store = $store;

return $this;
}







protected function event($event)
{
$this->events?->dispatch($event);
}






public function getEventDispatcher()
{
return $this->events;
}







public function setEventDispatcher(Dispatcher $events)
{
$this->events = $events;
}







public function offsetExists($key): bool
{
return $this->has($key);
}







public function offsetGet($key): mixed
{
return $this->get($key);
}








public function offsetSet($key, $value): void
{
$this->put($key, $value, $this->default);
}







public function offsetUnset($key): void
{
$this->forget($key);
}








public function __call($method, $parameters)
{
if (static::hasMacro($method)) {
return $this->macroCall($method, $parameters);
}

return $this->store->$method(...$parameters);
}






public function __clone()
{
$this->store = clone $this->store;
}
}
