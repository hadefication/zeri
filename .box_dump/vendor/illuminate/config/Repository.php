<?php

namespace Illuminate\Config;

use ArrayAccess;
use Illuminate\Contracts\Config\Repository as ConfigContract;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Traits\Macroable;
use InvalidArgumentException;

class Repository implements ArrayAccess, ConfigContract
{
use Macroable;






protected $items = [];






public function __construct(array $items = [])
{
$this->items = $items;
}







public function has($key)
{
return Arr::has($this->items, $key);
}








public function get($key, $default = null)
{
if (is_array($key)) {
return $this->getMany($key);
}

return Arr::get($this->items, $key, $default);
}







public function getMany($keys)
{
$config = [];

foreach ($keys as $key => $default) {
if (is_numeric($key)) {
[$key, $default] = [$default, null];
}

$config[$key] = Arr::get($this->items, $key, $default);
}

return $config;
}








public function string(string $key, $default = null): string
{
$value = $this->get($key, $default);

if (! is_string($value)) {
throw new InvalidArgumentException(
sprintf('Configuration value for key [%s] must be a string, %s given.', $key, gettype($value))
);
}

return $value;
}








public function integer(string $key, $default = null): int
{
$value = $this->get($key, $default);

if (! is_int($value)) {
throw new InvalidArgumentException(
sprintf('Configuration value for key [%s] must be an integer, %s given.', $key, gettype($value))
);
}

return $value;
}








public function float(string $key, $default = null): float
{
$value = $this->get($key, $default);

if (! is_float($value)) {
throw new InvalidArgumentException(
sprintf('Configuration value for key [%s] must be a float, %s given.', $key, gettype($value))
);
}

return $value;
}








public function boolean(string $key, $default = null): bool
{
$value = $this->get($key, $default);

if (! is_bool($value)) {
throw new InvalidArgumentException(
sprintf('Configuration value for key [%s] must be a boolean, %s given.', $key, gettype($value))
);
}

return $value;
}








public function array(string $key, $default = null): array
{
$value = $this->get($key, $default);

if (! is_array($value)) {
throw new InvalidArgumentException(
sprintf('Configuration value for key [%s] must be an array, %s given.', $key, gettype($value))
);
}

return $value;
}








public function collection(string $key, $default = null): Collection
{
return new Collection($this->array($key, $default));
}








public function set($key, $value = null)
{
$keys = is_array($key) ? $key : [$key => $value];

foreach ($keys as $key => $value) {
Arr::set($this->items, $key, $value);
}
}








public function prepend($key, $value)
{
$array = $this->get($key, []);

array_unshift($array, $value);

$this->set($key, $array);
}








public function push($key, $value)
{
$array = $this->get($key, []);

$array[] = $value;

$this->set($key, $array);
}






public function all()
{
return $this->items;
}







public function offsetExists($key): bool
{
return $this->has($key);
}







public function offsetGet($key): mixed
{
return $this->get($key);
}








public function offsetSet($key, $value): void
{
$this->set($key, $value);
}







public function offsetUnset($key): void
{
$this->set($key, null);
}
}
