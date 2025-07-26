<?php

namespace Illuminate\Support;

use ArrayAccess;
use ArrayIterator;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Traits\Conditionable;
use Illuminate\Support\Traits\InteractsWithData;
use Illuminate\Support\Traits\Macroable;
use IteratorAggregate;
use JsonSerializable;
use Traversable;

/**
@template
@template
@implements
@implements

*/
class Fluent implements Arrayable, ArrayAccess, IteratorAggregate, Jsonable, JsonSerializable
{
use Conditionable, InteractsWithData, Macroable {
__call as macroCall;
}






protected $attributes = [];






public function __construct($attributes = [])
{
$this->fill($attributes);
}







public static function make($attributes = [])
{
return new static($attributes);
}

/**
@template






*/
public function get($key, $default = null)
{
return data_get($this->attributes, $key, $default);
}








public function set($key, $value)
{
data_set($this->attributes, $key, $value);

return $this;
}







public function fill($attributes)
{
foreach ($attributes as $key => $value) {
$this->attributes[$key] = $value;
}

return $this;
}








public function value($key, $default = null)
{
if (array_key_exists($key, $this->attributes)) {
return $this->attributes[$key];
}

return value($default);
}








public function scope($key, $default = null)
{
return new static(
(array) $this->get($key, $default)
);
}







public function all($keys = null)
{
$data = $this->data();

if (! $keys) {
return $data;
}

$results = [];

foreach (is_array($keys) ? $keys : func_get_args() as $key) {
Arr::set($results, $key, Arr::get($data, $key));
}

return $results;
}








protected function data($key = null, $default = null)
{
return $this->get($key, $default);
}






public function getAttributes()
{
return $this->attributes;
}






public function toArray()
{
return $this->attributes;
}






public function jsonSerialize(): array
{
return $this->toArray();
}







public function toJson($options = 0)
{
return json_encode($this->jsonSerialize(), $options);
}






public function isEmpty(): bool
{
return empty($this->attributes);
}






public function isNotEmpty(): bool
{
return ! $this->isEmpty();
}







public function offsetExists($offset): bool
{
return isset($this->attributes[$offset]);
}







public function offsetGet($offset): mixed
{
return $this->value($offset);
}








public function offsetSet($offset, $value): void
{
$this->attributes[$offset] = $value;
}







public function offsetUnset($offset): void
{
unset($this->attributes[$offset]);
}






public function getIterator(): Traversable
{
return new ArrayIterator($this->attributes);
}








public function __call($method, $parameters)
{
if (static::hasMacro($method)) {
return $this->macroCall($method, $parameters);
}

$this->attributes[$method] = count($parameters) > 0 ? reset($parameters) : true;

return $this;
}







public function __get($key)
{
return $this->value($key);
}








public function __set($key, $value)
{
$this->offsetSet($key, $value);
}







public function __isset($key)
{
return $this->offsetExists($key);
}







public function __unset($key)
{
$this->offsetUnset($key);
}
}
