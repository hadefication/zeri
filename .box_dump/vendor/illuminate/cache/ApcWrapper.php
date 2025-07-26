<?php

namespace Illuminate\Cache;

class ApcWrapper
{






public function get($key)
{
$fetchedValue = apcu_fetch($key, $success);

return $success ? $fetchedValue : null;
}









public function put($key, $value, $seconds)
{
return apcu_store($key, $value, $seconds);
}








public function increment($key, $value)
{
return apcu_inc($key, $value);
}








public function decrement($key, $value)
{
return apcu_dec($key, $value);
}







public function delete($key)
{
return apcu_delete($key);
}






public function flush()
{
return apcu_clear_cache();
}
}
