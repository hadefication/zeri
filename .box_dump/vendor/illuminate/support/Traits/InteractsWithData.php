<?php

namespace Illuminate\Support\Traits;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Str;
use stdClass;

use function Illuminate\Support\enum_value;

trait InteractsWithData
{






abstract public function all($keys = null);








abstract protected function data($key = null, $default = null);







public function exists($key)
{
return $this->has($key);
}







public function has($key)
{
$keys = is_array($key) ? $key : func_get_args();

$data = $this->all();

foreach ($keys as $value) {
if (! Arr::has($data, $value)) {
return false;
}
}

return true;
}







public function hasAny($keys)
{
$keys = is_array($keys) ? $keys : func_get_args();

$data = $this->all();

return Arr::hasAny($data, $keys);
}









public function whenHas($key, callable $callback, ?callable $default = null)
{
if ($this->has($key)) {
return $callback(data_get($this->all(), $key)) ?: $this;
}

if ($default) {
return $default();
}

return $this;
}







public function filled($key)
{
$keys = is_array($key) ? $key : func_get_args();

foreach ($keys as $value) {
if ($this->isEmptyString($value)) {
return false;
}
}

return true;
}







public function isNotFilled($key)
{
$keys = is_array($key) ? $key : func_get_args();

foreach ($keys as $value) {
if (! $this->isEmptyString($value)) {
return false;
}
}

return true;
}







public function anyFilled($keys)
{
$keys = is_array($keys) ? $keys : func_get_args();

foreach ($keys as $key) {
if ($this->filled($key)) {
return true;
}
}

return false;
}









public function whenFilled($key, callable $callback, ?callable $default = null)
{
if ($this->filled($key)) {
return $callback(data_get($this->all(), $key)) ?: $this;
}

if ($default) {
return $default();
}

return $this;
}







public function missing($key)
{
$keys = is_array($key) ? $key : func_get_args();

return ! $this->has($keys);
}









public function whenMissing($key, callable $callback, ?callable $default = null)
{
if ($this->missing($key)) {
return $callback(data_get($this->all(), $key)) ?: $this;
}

if ($default) {
return $default();
}

return $this;
}







protected function isEmptyString($key)
{
$value = $this->data($key);

return ! is_bool($value) && ! is_array($value) && trim((string) $value) === '';
}








public function str($key, $default = null)
{
return $this->string($key, $default);
}








public function string($key, $default = null)
{
return Str::of($this->data($key, $default));
}










public function boolean($key = null, $default = false)
{
return filter_var($this->data($key, $default), FILTER_VALIDATE_BOOLEAN);
}








public function integer($key, $default = 0)
{
return intval($this->data($key, $default));
}








public function float($key, $default = 0.0)
{
return floatval($this->data($key, $default));
}











public function date($key, $format = null, $tz = null)
{
$tz = enum_value($tz);

if ($this->isNotFilled($key)) {
return null;
}

if (is_null($format)) {
return Date::parse($this->data($key), $tz);
}

return Date::createFromFormat($format, $this->data($key), $tz);
}

/**
@template







*/
public function enum($key, $enumClass, $default = null)
{
if ($this->isNotFilled($key) || ! $this->isBackedEnum($enumClass)) {
return value($default);
}

return $enumClass::tryFrom($this->data($key)) ?: value($default);
}

/**
@template






*/
public function enums($key, $enumClass)
{
if ($this->isNotFilled($key) || ! $this->isBackedEnum($enumClass)) {
return [];
}

return $this->collect($key)
->map(fn ($value) => $enumClass::tryFrom($value))
->filter()
->all();
}







protected function isBackedEnum($enumClass)
{
return enum_exists($enumClass) && method_exists($enumClass, 'tryFrom');
}







public function array($key = null)
{
return (array) (is_array($key) ? $this->only($key) : $this->data($key));
}







public function collect($key = null)
{
return new Collection(is_array($key) ? $this->only($key) : $this->data($key));
}







public function only($keys)
{
$results = [];

$data = $this->all();

$placeholder = new stdClass;

foreach (is_array($keys) ? $keys : func_get_args() as $key) {
$value = data_get($data, $key, $placeholder);

if ($value !== $placeholder) {
Arr::set($results, $key, $value);
}
}

return $results;
}







public function except($keys)
{
$keys = is_array($keys) ? $keys : func_get_args();

$results = $this->all();

Arr::forget($results, $keys);

return $results;
}
}
