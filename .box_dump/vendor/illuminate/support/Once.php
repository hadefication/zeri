<?php

namespace Illuminate\Support;

use WeakMap;

class Once
{





protected static ?self $instance = null;






protected static bool $enabled = true;






protected function __construct(protected WeakMap $values)
{

}






public static function instance()
{
return static::$instance ??= new static(new WeakMap);
}







public function value(Onceable $onceable)
{
if (! static::$enabled) {
return call_user_func($onceable->callable);
}

$object = $onceable->object ?: $this;

$hash = $onceable->hash;

if (! isset($this->values[$object])) {
$this->values[$object] = [];
}

if (array_key_exists($hash, $this->values[$object])) {
return $this->values[$object][$hash];
}

return $this->values[$object][$hash] = call_user_func($onceable->callable);
}






public static function enable()
{
static::$enabled = true;
}






public static function disable()
{
static::$enabled = false;
}






public static function flush()
{
static::$instance = null;
}
}
