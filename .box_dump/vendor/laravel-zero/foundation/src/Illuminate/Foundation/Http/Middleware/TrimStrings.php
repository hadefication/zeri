<?php

namespace Illuminate\Foundation\Http\Middleware;

use Closure;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class TrimStrings extends TransformsRequest
{





protected $except = [
'current_password',
'password',
'password_confirmation',
];






protected static $neverTrim = [];






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
$except = array_merge($this->except, static::$neverTrim);

if ($this->shouldSkip($key, $except) || ! is_string($value)) {
return $value;
}

return Str::trim($value);
}








protected function shouldSkip($key, $except)
{
return in_array($key, $except, true);
}







public static function except($attributes)
{
static::$neverTrim = array_values(array_unique(
array_merge(static::$neverTrim, Arr::wrap($attributes))
));
}







public static function skipWhen(Closure $callback)
{
static::$skipCallbacks[] = $callback;
}






public static function flushState()
{
static::$neverTrim = [];

static::$skipCallbacks = [];
}
}
