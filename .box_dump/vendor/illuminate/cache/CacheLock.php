<?php

namespace Illuminate\Cache;

class CacheLock extends Lock
{





protected $store;









public function __construct($store, $name, $seconds, $owner = null)
{
parent::__construct($name, $seconds, $owner);

$this->store = $store;
}






public function acquire()
{
if (method_exists($this->store, 'add') && $this->seconds > 0) {
return $this->store->add(
$this->name, $this->owner, $this->seconds
);
}

if (! is_null($this->store->get($this->name))) {
return false;
}

return ($this->seconds > 0)
? $this->store->put($this->name, $this->owner, $this->seconds)
: $this->store->forever($this->name, $this->owner);
}






public function release()
{
if ($this->isOwnedByCurrentProcess()) {
return $this->store->forget($this->name);
}

return false;
}






public function forceRelease()
{
$this->store->forget($this->name);
}






protected function getCurrentOwner()
{
return $this->store->get($this->name);
}
}
