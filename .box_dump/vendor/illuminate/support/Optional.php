<?php

namespace Illuminate\Support;

use ArrayAccess;
use ArrayObject;
use Illuminate\Support\Traits\Macroable;

class Optional implements ArrayAccess
{
use Macroable {
__call as macroCall;
}






protected $value;






public function __construct($value)
{
$this->value = $value;
}







public function __get($key)
{
if (is_object($this->value)) {
return $this->value->{$key} ?? null;
}
}







public function __isset($name)
{
if (is_object($this->value)) {
return isset($this->value->{$name});
}

if (is_array($this->value) || $this->value instanceof ArrayObject) {
return isset($this->value[$name]);
}

return false;
}







public function offsetExists($key): bool
{
return Arr::accessible($this->value) && Arr::exists($this->value, $key);
}







public function offsetGet($key): mixed
{
return Arr::get($this->value, $key);
}








public function offsetSet($key, $value): void
{
if (Arr::accessible($this->value)) {
$this->value[$key] = $value;
}
}







public function offsetUnset($key): void
{
if (Arr::accessible($this->value)) {
unset($this->value[$key]);
}
}








public function __call($method, $parameters)
{
if (static::hasMacro($method)) {
return $this->macroCall($method, $parameters);
}

if (is_object($this->value)) {
return $this->value->{$method}(...$parameters);
}
}
}
