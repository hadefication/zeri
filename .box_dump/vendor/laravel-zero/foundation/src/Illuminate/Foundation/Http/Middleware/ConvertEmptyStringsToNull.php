<?php

namespace Illuminate\Foundation\Http\Middleware;

use Closure;

class ConvertEmptyStringsToNull extends TransformsRequest
{





protected static $skipCallbacks = [];








public function handle($request, Closure $next)
{
foreach (static::$skipCallbacks as $callback) {
if ($callback($request)) {
return $next($request);
}
}

return parent::handle($request, $next);
}








protected function transform($key, $value)
{
return $value === '' ? null : $value;
}







public static function skipWhen(Closure $callback)
{
static::$skipCallbacks[] = $callback;
}






public static function flushState()
{
static::$skipCallbacks = [];
}
}
