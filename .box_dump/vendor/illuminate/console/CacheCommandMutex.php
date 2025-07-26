<?php

namespace Illuminate\Console;

use Carbon\CarbonInterval;
use Illuminate\Cache\DynamoDbStore;
use Illuminate\Contracts\Cache\Factory as Cache;
use Illuminate\Contracts\Cache\LockProvider;
use Illuminate\Support\InteractsWithTime;

class CacheCommandMutex implements CommandMutex
{
use InteractsWithTime;






public $cache;






public $store = null;






public function __construct(Cache $cache)
{
$this->cache = $cache;
}







public function create($command)
{
$store = $this->cache->store($this->store);

$expiresAt = method_exists($command, 'isolationLockExpiresAt')
? $command->isolationLockExpiresAt()
: CarbonInterval::hour();

if ($this->shouldUseLocks($store->getStore())) {
return $store->getStore()->lock(
$this->commandMutexName($command),
$this->secondsUntil($expiresAt)
)->get();
}

return $store->add($this->commandMutexName($command), true, $expiresAt);
}







public function exists($command)
{
$store = $this->cache->store($this->store);

if ($this->shouldUseLocks($store->getStore())) {
$lock = $store->getStore()->lock($this->commandMutexName($command));

return tap(! $lock->get(), function ($exists) use ($lock) {
if ($exists) {
$lock->release();
}
});
}

return $this->cache->store($this->store)->has($this->commandMutexName($command));
}







public function forget($command)
{
$store = $this->cache->store($this->store);

if ($this->shouldUseLocks($store->getStore())) {
return $store->getStore()->lock($this->commandMutexName($command))->forceRelease();
}

return $this->cache->store($this->store)->forget($this->commandMutexName($command));
}







protected function commandMutexName($command)
{
$baseName = 'framework'.DIRECTORY_SEPARATOR.'command-'.$command->getName();

return method_exists($command, 'isolatableId')
? $baseName.'-'.$command->isolatableId()
: $baseName;
}







public function useStore($store)
{
$this->store = $store;

return $this;
}







protected function shouldUseLocks($store)
{
return $store instanceof LockProvider && ! $store instanceof DynamoDbStore;
}
}
