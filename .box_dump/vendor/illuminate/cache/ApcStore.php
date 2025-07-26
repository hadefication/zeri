<?php

namespace Illuminate\Cache;

class ApcStore extends TaggableStore
{
use RetrievesMultipleKeys;






protected $apc;






protected $prefix;







public function __construct(ApcWrapper $apc, $prefix = '')
{
$this->apc = $apc;
$this->prefix = $prefix;
}







public function get($key)
{
return $this->apc->get($this->prefix.$key);
}









public function put($key, $value, $seconds)
{
return $this->apc->put($this->prefix.$key, $value, $seconds);
}








public function increment($key, $value = 1)
{
return $this->apc->increment($this->prefix.$key, $value);
}








public function decrement($key, $value = 1)
{
return $this->apc->decrement($this->prefix.$key, $value);
}








public function forever($key, $value)
{
return $this->put($key, $value, 0);
}







public function forget($key)
{
return $this->apc->delete($this->prefix.$key);
}






public function flush()
{
return $this->apc->flush();
}






public function getPrefix()
{
return $this->prefix;
}







public function setPrefix($prefix)
{
$this->prefix = $prefix;
}
}
