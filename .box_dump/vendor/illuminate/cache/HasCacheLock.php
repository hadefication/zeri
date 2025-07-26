<?php

namespace Illuminate\Cache;

trait HasCacheLock
{








public function lock($name, $seconds = 0, $owner = null)
{
return new CacheLock($this, $name, $seconds, $owner);
}








public function restoreLock($name, $owner)
{
return $this->lock($name, 0, $owner);
}
}
