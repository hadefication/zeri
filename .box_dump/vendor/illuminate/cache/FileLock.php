<?php

namespace Illuminate\Cache;

class FileLock extends CacheLock
{





public function acquire()
{
return $this->store->add($this->name, $this->owner, $this->seconds);
}
}
