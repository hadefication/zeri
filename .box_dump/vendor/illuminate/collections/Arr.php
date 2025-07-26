<?php

namespace Illuminate\Support;

use ArgumentCountError;
use ArrayAccess;
use Closure;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Traits\Macroable;
use InvalidArgumentException;
use JsonSerializable;
use Random\Randomizer;
use Traversable;
use WeakMap;

class Arr
{
use Macroable;







public static function accessible($value)
{
return is_array($value) || $value instanceof ArrayAccess;
}







public static function arrayable($value)
{
return is_array($value)
|| $value instanceof Arrayable
|| $value instanceof Traversable
|| $value instanceof Jsonable
|| $value instanceof JsonSerializable;
}









public static function add($array, $key, $value)
{
if (is_null(static::get($array, $key))) {
static::set($array, $key, $value);
}

return $array;
}




public static function array(ArrayAccess|array $array, string|int|null $key, ?array $default = null): array
{
$value = Arr::get($array, $key, $default);

if (! is_array($value)) {
throw new InvalidArgumentException(
sprintf('Array value for key [%s] must be an array, %s found.', $key, gettype($value))
);
}

return $value;
}




public static function boolean(ArrayAccess|array $array, string|int|null $key, ?bool $default = null): bool
{
$value = Arr::get($array, $key, $default);

if (! is_bool($value)) {
throw new InvalidArgumentException(
sprintf('Array value for key [%s] must be a boolean, %s found.', $key, gettype($value))
);
}

return $value;
}







public static function collapse($array)
{
$results = [];

foreach ($array as $values) {
if ($values instanceof Collection) {
$values = $values->all();
} elseif (! is_array($values)) {
continue;
}

$results[] = $values;
}

return array_merge([], ...$results);
}







public static function crossJoin(...$arrays)
{
$results = [[]];

foreach ($arrays as $index => $array) {
$append = [];

foreach ($results as $product) {
foreach ($array as $item) {
$product[$index] = $item;

$append[] = $product;
}
}

$results = $append;
}

return $results;
}







public static function divide($array)
{
return [array_keys($array), array_values($array)];
}








public static function dot($array, $prepend = '')
{
$results = [];

$flatten = function ($data, $prefix) use (&$results, &$flatten): void {
foreach ($data as $key => $value) {
$newKey = $prefix.$key;

if (is_array($value) && ! empty($value)) {
$flatten($value, $newKey.'.');
} else {
$results[$newKey] = $value;
}
}
};

$flatten($array, $prepend);

return $results;
}







public static function undot($array)
{
$results = [];

foreach ($array as $key => $value) {
static::set($results, $key, $value);
}

return $results;
}








public static function except($array, $keys)
{
static::forget($array, $keys);

return $array;
}








public static function exists($array, $key)
{
if ($array instanceof Enumerable) {
return $array->has($key);
}

if ($array instanceof ArrayAccess) {
return $array->offsetExists($key);
}

if (is_float($key)) {
$key = (string) $key;
}

return array_key_exists($key, $array);
}

/**
@template
@template
@template







*/
public static function first($array, ?callable $callback = null, $default = null)
{
if (is_null($callback)) {
if (empty($array)) {
return value($default);
}

foreach ($array as $item) {
return $item;
}

return value($default);
}

foreach ($array as $key => $value) {
if ($callback($value, $key)) {
return $value;
}
}

return value($default);
}

/**
@template
@template
@template







*/
public static function last($array, ?callable $callback = null, $default = null)
{
if (is_null($callback)) {
return empty($array) ? value($default) : end($array);
}

return static::first(array_reverse($array, true), $callback, $default);
}








public static function take($array, $limit)
{
if ($limit < 0) {
return array_slice($array, $limit, abs($limit));
}

return array_slice($array, 0, $limit);
}








public static function flatten($array, $depth = INF)
{
$result = [];

foreach ($array as $item) {
$item = $item instanceof Collection ? $item->all() : $item;

if (! is_array($item)) {
$result[] = $item;
} else {
$values = $depth === 1
? array_values($item)
: static::flatten($item, $depth - 1);

foreach ($values as $value) {
$result[] = $value;
}
}
}

return $result;
}




public static function float(ArrayAccess|array $array, string|int|null $key, ?float $default = null): float
{
$value = Arr::get($array, $key, $default);

if (! is_float($value)) {
throw new InvalidArgumentException(
sprintf('Array value for key [%s] must be a float, %s found.', $key, gettype($value))
);
}

return $value;
}








public static function forget(&$array, $keys)
{
$original = &$array;

$keys = (array) $keys;

if (count($keys) === 0) {
return;
}

foreach ($keys as $key) {

if (static::exists($array, $key)) {
unset($array[$key]);

continue;
}

$parts = explode('.', $key);


$array = &$original;

while (count($parts) > 1) {
$part = array_shift($parts);

if (isset($array[$part]) && static::accessible($array[$part])) {
$array = &$array[$part];
} else {
continue 2;
}
}

unset($array[array_shift($parts)]);
}
}

/**
@template
@template







*/
public static function from($items)
{
return match (true) {
is_array($items) => $items,
$items instanceof Enumerable => $items->all(),
$items instanceof Arrayable => $items->toArray(),
$items instanceof WeakMap => iterator_to_array($items, false),
$items instanceof Traversable => iterator_to_array($items),
$items instanceof Jsonable => json_decode($items->toJson(), true),
$items instanceof JsonSerializable => (array) $items->jsonSerialize(),
is_object($items) => (array) $items,
default => throw new InvalidArgumentException('Items cannot be represented by a scalar value.'),
};
}









public static function get($array, $key, $default = null)
{
if (! static::accessible($array)) {
return value($default);
}

if (is_null($key)) {
return $array;
}

if (static::exists($array, $key)) {
return $array[$key];
}

if (! str_contains($key, '.')) {
return $array[$key] ?? value($default);
}

foreach (explode('.', $key) as $segment) {
if (static::accessible($array) && static::exists($array, $segment)) {
$array = $array[$segment];
} else {
return value($default);
}
}

return $array;
}








public static function has($array, $keys)
{
$keys = (array) $keys;

if (! $array || $keys === []) {
return false;
}

foreach ($keys as $key) {
$subKeyArray = $array;

if (static::exists($array, $key)) {
continue;
}

foreach (explode('.', $key) as $segment) {
if (static::accessible($subKeyArray) && static::exists($subKeyArray, $segment)) {
$subKeyArray = $subKeyArray[$segment];
} else {
return false;
}
}
}

return true;
}








public static function hasAll($array, $keys)
{
$keys = (array) $keys;

if (! $array || $keys === []) {
return false;
}

foreach ($keys as $key) {
if (! static::has($array, $key)) {
return false;
}
}

return true;
}








public static function hasAny($array, $keys)
{
if (is_null($keys)) {
return false;
}

$keys = (array) $keys;

if (! $array) {
return false;
}

if ($keys === []) {
return false;
}

foreach ($keys as $key) {
if (static::has($array, $key)) {
return true;
}
}

return false;
}




public static function integer(ArrayAccess|array $array, string|int|null $key, ?int $default = null): int
{
$value = Arr::get($array, $key, $default);

if (! is_int($value)) {
throw new InvalidArgumentException(
sprintf('Array value for key [%s] must be an integer, %s found.', $key, gettype($value))
);
}

return $value;
}









public static function isAssoc(array $array)
{
return ! array_is_list($array);
}









public static function isList($array)
{
return array_is_list($array);
}









public static function join($array, $glue, $finalGlue = '')
{
if ($finalGlue === '') {
return implode($glue, $array);
}

if (count($array) === 0) {
return '';
}

if (count($array) === 1) {
return end($array);
}

$finalItem = array_pop($array);

return implode($glue, $array).$finalGlue.$finalItem;
}








public static function keyBy($array, $keyBy)
{
return (new Collection($array))->keyBy($keyBy)->all();
}








public static function prependKeysWith($array, $prependWith)
{
return static::mapWithKeys($array, fn ($item, $key) => [$prependWith.$key => $item]);
}








public static function only($array, $keys)
{
return array_intersect_key($array, array_flip((array) $keys));
}








public static function select($array, $keys)
{
$keys = static::wrap($keys);

return static::map($array, function ($item) use ($keys) {
$result = [];

foreach ($keys as $key) {
if (Arr::accessible($item) && Arr::exists($item, $key)) {
$result[$key] = $item[$key];
} elseif (is_object($item) && isset($item->{$key})) {
$result[$key] = $item->{$key};
}
}

return $result;
});
}









public static function pluck($array, $value, $key = null)
{
$results = [];

[$value, $key] = static::explodePluckParameters($value, $key);

foreach ($array as $item) {
$itemValue = $value instanceof Closure
? $value($item)
: data_get($item, $value);




if (is_null($key)) {
$results[] = $itemValue;
} else {
$itemKey = $key instanceof Closure
? $key($item)
: data_get($item, $key);

if (is_object($itemKey) && method_exists($itemKey, '__toString')) {
$itemKey = (string) $itemKey;
}

$results[$itemKey] = $itemValue;
}
}

return $results;
}








protected static function explodePluckParameters($value, $key)
{
$value = is_string($value) ? explode('.', $value) : $value;

$key = is_null($key) || is_array($key) || $key instanceof Closure ? $key : explode('.', $key);

return [$value, $key];
}








public static function map(array $array, callable $callback)
{
$keys = array_keys($array);

try {
$items = array_map($callback, $array, $keys);
} catch (ArgumentCountError) {
$items = array_map($callback, $array);
}

return array_combine($keys, $items);
}

/**
@template
@template
@template
@template








*/
public static function mapWithKeys(array $array, callable $callback)
{
$result = [];

foreach ($array as $key => $value) {
$assoc = $callback($value, $key);

foreach ($assoc as $mapKey => $mapValue) {
$result[$mapKey] = $mapValue;
}
}

return $result;
}

/**
@template
@template






*/
public static function mapSpread(array $array, callable $callback)
{
return static::map($array, function ($chunk, $key) use ($callback) {
$chunk[] = $key;

return $callback(...$chunk);
});
}









public static function prepend($array, $value, $key = null)
{
if (func_num_args() == 2) {
array_unshift($array, $value);
} else {
$array = [$key => $value] + $array;
}

return $array;
}









public static function pull(&$array, $key, $default = null)
{
$value = static::get($array, $key, $default);

static::forget($array, $key);

return $value;
}







public static function query($array)
{
return http_build_query($array, '', '&', PHP_QUERY_RFC3986);
}











public static function random($array, $number = null, $preserveKeys = false)
{
$requested = is_null($number) ? 1 : $number;

$count = count($array);

if ($requested > $count) {
throw new InvalidArgumentException(
"You requested {$requested} items, but there are only {$count} items available."
);
}

if (empty($array) || (! is_null($number) && $number <= 0)) {
return is_null($number) ? null : [];
}

$keys = (new Randomizer)->pickArrayKeys($array, $requested);

if (is_null($number)) {
return $array[$keys[0]];
}

$results = [];

if ($preserveKeys) {
foreach ($keys as $key) {
$results[$key] = $array[$key];
}
} else {
foreach ($keys as $key) {
$results[] = $array[$key];
}
}

return $results;
}











public static function set(&$array, $key, $value)
{
if (is_null($key)) {
return $array = $value;
}

$keys = explode('.', $key);

foreach ($keys as $i => $key) {
if (count($keys) === 1) {
break;
}

unset($keys[$i]);




if (! isset($array[$key]) || ! is_array($array[$key])) {
$array[$key] = [];
}

$array = &$array[$key];
}

$array[array_shift($keys)] = $value;

return $array;
}







public static function shuffle($array)
{
return (new Randomizer)->shuffleArray($array);
}










public static function sole($array, ?callable $callback = null)
{
if ($callback) {
$array = static::where($array, $callback);
}

$count = count($array);

if ($count === 0) {
throw new ItemNotFoundException;
}

if ($count > 1) {
throw new MultipleItemsFoundException($count);
}

return static::first($array);
}








public static function sort($array, $callback = null)
{
return (new Collection($array))->sortBy($callback)->all();
}








public static function sortDesc($array, $callback = null)
{
return (new Collection($array))->sortByDesc($callback)->all();
}









public static function sortRecursive($array, $options = SORT_REGULAR, $descending = false)
{
foreach ($array as &$value) {
if (is_array($value)) {
$value = static::sortRecursive($value, $options, $descending);
}
}

if (! array_is_list($array)) {
$descending
? krsort($array, $options)
: ksort($array, $options);
} else {
$descending
? rsort($array, $options)
: sort($array, $options);
}

return $array;
}








public static function sortRecursiveDesc($array, $options = SORT_REGULAR)
{
return static::sortRecursive($array, $options, true);
}




public static function string(ArrayAccess|array $array, string|int|null $key, ?string $default = null): string
{
$value = Arr::get($array, $key, $default);

if (! is_string($value)) {
throw new InvalidArgumentException(
sprintf('Array value for key [%s] must be a string, %s found.', $key, gettype($value))
);
}

return $value;
}







public static function toCssClasses($array)
{
$classList = static::wrap($array);

$classes = [];

foreach ($classList as $class => $constraint) {
if (is_numeric($class)) {
$classes[] = $constraint;
} elseif ($constraint) {
$classes[] = $class;
}
}

return implode(' ', $classes);
}







public static function toCssStyles($array)
{
$styleList = static::wrap($array);

$styles = [];

foreach ($styleList as $class => $constraint) {
if (is_numeric($class)) {
$styles[] = Str::finish($constraint, ';');
} elseif ($constraint) {
$styles[] = Str::finish($class, ';');
}
}

return implode(' ', $styles);
}








public static function where($array, callable $callback)
{
return array_filter($array, $callback, ARRAY_FILTER_USE_BOTH);
}








public static function reject($array, callable $callback)
{
return static::where($array, fn ($value, $key) => ! $callback($value, $key));
}

/**
@template
@template






*/
public static function partition($array, callable $callback)
{
$passed = [];
$failed = [];

foreach ($array as $key => $item) {
if ($callback($item, $key)) {
$passed[$key] = $item;
} else {
$failed[$key] = $item;
}
}

return [$passed, $failed];
}







public static function whereNotNull($array)
{
return static::where($array, fn ($value) => ! is_null($value));
}







public static function wrap($value)
{
if (is_null($value)) {
return [];
}

return is_array($value) ? $value : [$value];
}
}
