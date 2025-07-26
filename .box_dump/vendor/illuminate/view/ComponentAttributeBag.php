<?php

namespace Illuminate\View;

use ArrayAccess;
use ArrayIterator;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Conditionable;
use Illuminate\Support\Traits\Macroable;
use IteratorAggregate;
use JsonSerializable;
use Stringable;
use Traversable;

class ComponentAttributeBag implements Arrayable, ArrayAccess, IteratorAggregate, JsonSerializable, Htmlable, Stringable
{
use Conditionable, Macroable;






protected $attributes = [];






public function __construct(array $attributes = [])
{
$this->setAttributes($attributes);
}






public function all()
{
return $this->attributes;
}







public function first($default = null)
{
return $this->getIterator()->current() ?? value($default);
}








public function get($key, $default = null)
{
return $this->attributes[$key] ?? value($default);
}







public function has($key)
{
$keys = is_array($key) ? $key : func_get_args();

foreach ($keys as $value) {
if (! array_key_exists($value, $this->attributes)) {
return false;
}
}

return true;
}







public function hasAny($key)
{
if (! count($this->attributes)) {
return false;
}

$keys = is_array($key) ? $key : func_get_args();

foreach ($keys as $value) {
if ($this->has($value)) {
return true;
}
}

return false;
}







public function missing($key)
{
return ! $this->has($key);
}







public function only($keys)
{
if (is_null($keys)) {
$values = $this->attributes;
} else {
$keys = Arr::wrap($keys);

$values = Arr::only($this->attributes, $keys);
}

return new static($values);
}







public function except($keys)
{
if (is_null($keys)) {
$values = $this->attributes;
} else {
$keys = Arr::wrap($keys);

$values = Arr::except($this->attributes, $keys);
}

return new static($values);
}







public function filter($callback)
{
return new static((new Collection($this->attributes))->filter($callback)->all());
}







public function whereStartsWith($needles)
{
return $this->filter(function ($value, $key) use ($needles) {
return Str::startsWith($key, $needles);
});
}







public function whereDoesntStartWith($needles)
{
return $this->filter(function ($value, $key) use ($needles) {
return ! Str::startsWith($key, $needles);
});
}







public function thatStartWith($needles)
{
return $this->whereStartsWith($needles);
}







public function onlyProps($keys)
{
return $this->only(static::extractPropNames($keys));
}







public function exceptProps($keys)
{
return $this->except(static::extractPropNames($keys));
}







public function class($classList)
{
$classList = Arr::wrap($classList);

return $this->merge(['class' => Arr::toCssClasses($classList)]);
}







public function style($styleList)
{
$styleList = Arr::wrap($styleList);

return $this->merge(['style' => Arr::toCssStyles($styleList)]);
}








public function merge(array $attributeDefaults = [], $escape = true)
{
$attributeDefaults = array_map(function ($value) use ($escape) {
return $this->shouldEscapeAttributeValue($escape, $value)
? e($value)
: $value;
}, $attributeDefaults);

[$appendableAttributes, $nonAppendableAttributes] = (new Collection($this->attributes))
->partition(function ($value, $key) use ($attributeDefaults) {
return $key === 'class' || $key === 'style' || (
isset($attributeDefaults[$key]) &&
$attributeDefaults[$key] instanceof AppendableAttributeValue
);
});

$attributes = $appendableAttributes->mapWithKeys(function ($value, $key) use ($attributeDefaults, $escape) {
$defaultsValue = isset($attributeDefaults[$key]) && $attributeDefaults[$key] instanceof AppendableAttributeValue
? $this->resolveAppendableAttributeDefault($attributeDefaults, $key, $escape)
: ($attributeDefaults[$key] ?? '');

if ($key === 'style') {
$value = Str::finish($value, ';');
}

return [$key => implode(' ', array_unique(array_filter([$defaultsValue, $value])))];
})->merge($nonAppendableAttributes)->all();

return new static(array_merge($attributeDefaults, $attributes));
}








protected function shouldEscapeAttributeValue($escape, $value)
{
if (! $escape) {
return false;
}

return ! is_object($value) &&
! is_null($value) &&
! is_bool($value);
}







public function prepends($value)
{
return new AppendableAttributeValue($value);
}









protected function resolveAppendableAttributeDefault($attributeDefaults, $key, $escape)
{
if ($this->shouldEscapeAttributeValue($escape, $value = $attributeDefaults[$key]->value)) {
$value = e($value);
}

return $value;
}






public function isEmpty()
{
return trim((string) $this) === '';
}






public function isNotEmpty()
{
return ! $this->isEmpty();
}






public function getAttributes()
{
return $this->attributes;
}







public function setAttributes(array $attributes)
{
if (isset($attributes['attributes']) &&
$attributes['attributes'] instanceof self) {
$parentBag = $attributes['attributes'];

unset($attributes['attributes']);

$attributes = $parentBag->merge($attributes, $escape = false)->getAttributes();
}

$this->attributes = $attributes;
}







public static function extractPropNames(array $keys)
{
$props = [];

foreach ($keys as $key => $default) {
$key = is_numeric($key) ? $default : $key;

$props[] = $key;
$props[] = Str::kebab($key);
}

return $props;
}






public function toHtml()
{
return (string) $this;
}







public function __invoke(array $attributeDefaults = [])
{
return new HtmlString((string) $this->merge($attributeDefaults));
}







public function offsetExists($offset): bool
{
return isset($this->attributes[$offset]);
}







public function offsetGet($offset): mixed
{
return $this->get($offset);
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






public function jsonSerialize(): mixed
{
return $this->attributes;
}






public function toArray()
{
return $this->all();
}






public function __toString()
{
$string = '';

foreach ($this->attributes as $key => $value) {
if ($value === false || is_null($value)) {
continue;
}

if ($value === true) {
$value = $key === 'x-data' || str_starts_with($key, 'wire:') ? '' : $key;
}

$string .= ' '.$key.'="'.str_replace('"', '\\"', trim($value)).'"';
}

return trim($string);
}
}
