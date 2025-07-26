<?php

namespace Illuminate\Cache;

class MemcachedLock extends Lock
{





protected $memcached;









public function __construct($memcached, $name, $seconds, $owner = null)
{
parent::__construct($name, $seconds, $owner);

$this->memcached = $memcached;
}






public function acquire()
{
return $this->memcached->add(
$this->name, $this->owner, $this->seconds
);
}






public function release()
{
if ($this->isOwnedByCurrentProcess()) {
return $this->memcached->delete($this->name);
}

return false;
}






public function forceRelease()
{
$this->memcached->delete($this->name);
}






protected function getCurrentOwner()
{
return $this->memcached->get($this->name);
}
}
