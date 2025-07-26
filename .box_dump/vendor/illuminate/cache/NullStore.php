<?php

namespace Illuminate\Cache;

use Illuminate\Contracts\Cache\LockProvider;

class NullStore extends TaggableStore implements LockProvider
{
use RetrievesMultipleKeys;







public function get($key)
{

}









public function put($key, $value, $seconds)
{
return false;
}








public function increment($key, $value = 1)
{
return false;
}








public function decrement($key, $value = 1)
{
return false;
}








public function forever($key, $value)
{
return false;
}









public function lock($name, $seconds = 0, $owner = null)
{
return new NoLock($name, $seconds, $owner);
}








public function restoreLock($name, $owner)
{
return $this->lock($name, 0, $owner);
}







public function forget($key)
{
return true;
}






public function flush()
{
return true;
}






public function getPrefix()
{
return '';
}
}
