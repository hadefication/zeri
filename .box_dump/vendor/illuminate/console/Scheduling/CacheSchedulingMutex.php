<?php

namespace Illuminate\Console\Scheduling;

use DateTimeInterface;
use Illuminate\Contracts\Cache\Factory as Cache;

class CacheSchedulingMutex implements SchedulingMutex, CacheAware
{





public $cache;






public $store;






public function __construct(Cache $cache)
{
$this->cache = $cache;
}








public function create(Event $event, DateTimeInterface $time)
{
return $this->cache->store($this->store)->add(
$event->mutexName().$time->format('Hi'), true, 3600
);
}








public function exists(Event $event, DateTimeInterface $time)
{
return $this->cache->store($this->store)->has(
$event->mutexName().$time->format('Hi')
);
}







public function useStore($store)
{
$this->store = $store;

return $this;
}
}
