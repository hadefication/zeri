<?php

namespace Illuminate\Console\Scheduling;

use Illuminate\Cache\DynamoDbStore;
use Illuminate\Contracts\Cache\Factory as Cache;
use Illuminate\Contracts\Cache\LockProvider;

class CacheEventMutex implements EventMutex, CacheAware
{





public $cache;






public $store;






public function __construct(Cache $cache)
{
$this->cache = $cache;
}







public function create(Event $event)
{
if ($this->shouldUseLocks($this->cache->store($this->store)->getStore())) {
return $this->cache->store($this->store)->getStore()
->lock($event->mutexName(), $event->expiresAt * 60)
->acquire();
}

return $this->cache->store($this->store)->add(
$event->mutexName(), true, $event->expiresAt * 60
);
}







public function exists(Event $event)
{
if ($this->shouldUseLocks($this->cache->store($this->store)->getStore())) {
return ! $this->cache->store($this->store)->getStore()
->lock($event->mutexName(), $event->expiresAt * 60)
->get(fn () => true);
}

return $this->cache->store($this->store)->has($event->mutexName());
}







public function forget(Event $event)
{
if ($this->shouldUseLocks($this->cache->store($this->store)->getStore())) {
$this->cache->store($this->store)->getStore()
->lock($event->mutexName(), $event->expiresAt * 60)
->forceRelease();

return;
}

$this->cache->store($this->store)->forget($event->mutexName());
}







protected function shouldUseLocks($store)
{
return $store instanceof LockProvider && ! $store instanceof DynamoDbStore;
}







public function useStore($store)
{
$this->store = $store;

return $this;
}
}
