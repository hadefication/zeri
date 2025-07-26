<?php

namespace Illuminate\Cache;

use Illuminate\Redis\Connections\PhpRedisConnection;

class PhpRedisLock extends RedisLock
{








public function __construct(PhpRedisConnection $redis, string $name, int $seconds, ?string $owner = null)
{
parent::__construct($redis, $name, $seconds, $owner);
}




public function release()
{
return (bool) $this->redis->eval(
LuaScripts::releaseLock(),
1,
$this->name,
...$this->redis->pack([$this->owner])
);
}
}
