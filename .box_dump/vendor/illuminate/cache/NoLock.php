<?php

namespace Illuminate\Cache;

class NoLock extends Lock
{





public function acquire()
{
return true;
}






public function release()
{
return true;
}






public function forceRelease()
{

}






protected function getCurrentOwner()
{
return $this->owner;
}
}
