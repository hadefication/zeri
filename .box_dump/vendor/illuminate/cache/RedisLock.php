<?php

namespace Illuminate\Cache;

class RedisLock extends Lock
{





protected $redis;









public function __construct($redis, $name, $seconds, $owner = null)
{
parent::__construct($name, $seconds, $owner);

$this->redis = $redis;
}






public function acquire()
{
if ($this->seconds > 0) {
return $this->redis->set($this->name, $this->owner, 'EX', $this->seconds, 'NX') == true;
}

return $this->redis->setnx($this->name, $this->owner) === 1;
}






public function release()
{
return (bool) $this->redis->eval(LuaScripts::releaseLock(), 1, $this->name, $this->owner);
}






public function forceRelease()
{
$this->redis->del($this->name);
}






protected function getCurrentOwner()
{
return $this->redis->get($this->name);
}






public function getConnectionName()
{
return $this->redis->getName();
}
}
