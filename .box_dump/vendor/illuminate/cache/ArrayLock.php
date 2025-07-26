<?php

namespace Illuminate\Cache;

use Illuminate\Support\Carbon;

class ArrayLock extends Lock
{





protected $store;









public function __construct($store, $name, $seconds, $owner = null)
{
parent::__construct($name, $seconds, $owner);

$this->store = $store;
}






public function acquire()
{
$expiration = $this->store->locks[$this->name]['expiresAt'] ?? Carbon::now()->addSecond();

if ($this->exists() && $expiration->isFuture()) {
return false;
}

$this->store->locks[$this->name] = [
'owner' => $this->owner,
'expiresAt' => $this->seconds === 0 ? null : Carbon::now()->addSeconds($this->seconds),
];

return true;
}






protected function exists()
{
return isset($this->store->locks[$this->name]);
}






public function release()
{
if (! $this->exists()) {
return false;
}

if (! $this->isOwnedByCurrentProcess()) {
return false;
}

$this->forceRelease();

return true;
}






protected function getCurrentOwner()
{
if (! $this->exists()) {
return null;
}

return $this->store->locks[$this->name]['owner'];
}






public function forceRelease()
{
unset($this->store->locks[$this->name]);
}
}
