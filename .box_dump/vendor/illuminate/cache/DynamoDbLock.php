<?php

namespace Illuminate\Cache;

class DynamoDbLock extends Lock
{





protected $dynamo;









public function __construct(DynamoDbStore $dynamo, $name, $seconds, $owner = null)
{
parent::__construct($name, $seconds, $owner);

$this->dynamo = $dynamo;
}






public function acquire()
{
if ($this->seconds > 0) {
return $this->dynamo->add($this->name, $this->owner, $this->seconds);
}

return $this->dynamo->add($this->name, $this->owner, 86400);
}






public function release()
{
if ($this->isOwnedByCurrentProcess()) {
return $this->dynamo->forget($this->name);
}

return false;
}






public function forceRelease()
{
$this->dynamo->forget($this->name);
}






protected function getCurrentOwner()
{
return $this->dynamo->get($this->name);
}
}
