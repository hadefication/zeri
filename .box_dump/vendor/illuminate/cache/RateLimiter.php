<?php

namespace Illuminate\Cache;

use Closure;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Redis\Connections\PhpRedisConnection;
use Illuminate\Support\Collection;
use Illuminate\Support\InteractsWithTime;

use function Illuminate\Support\enum_value;

class RateLimiter
{
use InteractsWithTime;






protected $cache;






protected $limiters = [];






public function __construct(Cache $cache)
{
$this->cache = $cache;
}








public function for($name, Closure $callback)
{
$resolvedName = $this->resolveLimiterName($name);

$this->limiters[$resolvedName] = $callback;

return $this;
}







public function limiter($name)
{
$resolvedName = $this->resolveLimiterName($name);

$limiter = $this->limiters[$resolvedName] ?? null;

if (! is_callable($limiter)) {
return;
}

return function (...$args) use ($limiter) {
$result = $limiter(...$args);

if (! is_array($result)) {
return $result;
}

$duplicates = (new Collection($result))->duplicates('key');

if ($duplicates->isEmpty()) {
return $result;
}

foreach ($result as $limit) {
if ($duplicates->contains($limit->key)) {
$limit->key = $limit->fallbackKey();
}
}

return $result;
};
}










public function attempt($key, $maxAttempts, Closure $callback, $decaySeconds = 60)
{
if ($this->tooManyAttempts($key, $maxAttempts)) {
return false;
}

if (is_null($result = $callback())) {
$result = true;
}

return tap($result, function () use ($key, $decaySeconds) {
$this->hit($key, $decaySeconds);
});
}








public function tooManyAttempts($key, $maxAttempts)
{
if ($this->attempts($key) >= $maxAttempts) {
if ($this->cache->has($this->cleanRateLimiterKey($key).':timer')) {
return true;
}

$this->resetAttempts($key);
}

return false;
}








public function hit($key, $decaySeconds = 60)
{
return $this->increment($key, $decaySeconds);
}









public function increment($key, $decaySeconds = 60, $amount = 1)
{
$key = $this->cleanRateLimiterKey($key);

$this->cache->add(
$key.':timer', $this->availableAt($decaySeconds), $decaySeconds
);

$added = $this->withoutSerializationOrCompression(
fn () => $this->cache->add($key, 0, $decaySeconds)
);

$hits = (int) $this->cache->increment($key, $amount);

if (! $added && $hits == 1) {
$this->withoutSerializationOrCompression(
fn () => $this->cache->put($key, 1, $decaySeconds)
);
}

return $hits;
}









public function decrement($key, $decaySeconds = 60, $amount = 1)
{
return $this->increment($key, $decaySeconds, $amount * -1);
}







public function attempts($key)
{
$key = $this->cleanRateLimiterKey($key);

return $this->withoutSerializationOrCompression(fn () => $this->cache->get($key, 0));
}







public function resetAttempts($key)
{
$key = $this->cleanRateLimiterKey($key);

return $this->cache->forget($key);
}








public function remaining($key, $maxAttempts)
{
$key = $this->cleanRateLimiterKey($key);

$attempts = $this->attempts($key);

return $maxAttempts - $attempts;
}








public function retriesLeft($key, $maxAttempts)
{
return $this->remaining($key, $maxAttempts);
}







public function clear($key)
{
$key = $this->cleanRateLimiterKey($key);

$this->resetAttempts($key);

$this->cache->forget($key.':timer');
}







public function availableIn($key)
{
$key = $this->cleanRateLimiterKey($key);

return max(0, $this->cache->get($key.':timer') - $this->currentTime());
}







public function cleanRateLimiterKey($key)
{
return preg_replace('/&([a-z])[a-z]+;/i', '$1', htmlentities($key));
}







protected function withoutSerializationOrCompression(callable $callback)
{
$store = $this->cache->getStore();

if (! $store instanceof RedisStore) {
return $callback();
}

$connection = $store->connection();

if (! $connection instanceof PhpRedisConnection) {
return $callback();
}

return $connection->withoutSerializationOrCompression($callback);
}







private function resolveLimiterName($name): string
{
return (string) enum_value($name);
}
}
