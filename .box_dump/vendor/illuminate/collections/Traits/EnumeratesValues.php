<?php

namespace Illuminate\Support\Traits;

use BackedEnum;
use CachingIterator;
use Closure;
use Exception;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Enumerable;
use Illuminate\Support\HigherOrderCollectionProxy;
use JsonSerializable;
use UnexpectedValueException;
use UnitEnum;

use function Illuminate\Support\enum_value;

/**
@template
@template-covariant
@property-read
@property-read
@property-read
@property-read
@property-read
@property-read
@property-read
@property-read
@property-read
@property-read
@property-read
@property-read
@property-read
@property-read
@property-read
@property-read
@property-read
@property-read
@property-read
@property-read
@property-read
@property-read
@property-read
@property-read
@property-read
@property-read
@property-read
@property-read
@property-read
@property-read


*/
trait EnumeratesValues
{
use Conditionable;






protected $escapeWhenCastingToString = false;






protected static $proxies = [
'average',
'avg',
'contains',
'doesntContain',
'each',
'every',
'filter',
'first',
'flatMap',
'groupBy',
'keyBy',
'last',
'map',
'max',
'min',
'partition',
'percentage',
'reject',
'skipUntil',
'skipWhile',
'some',
'sortBy',
'sortByDesc',
'sum',
'takeUntil',
'takeWhile',
'unique',
'unless',
'until',
'when',
];

/**
@template
@template





*/
public static function make($items = [])
{
return new static($items);
}

/**
@template





*/
public static function wrap($value)
{
return $value instanceof Enumerable
? new static($value)
: new static(Arr::wrap($value));
}

/**
@template
@template





*/
public static function unwrap($value)
{
return $value instanceof Enumerable ? $value->all() : $value;
}






public static function empty()
{
return new static([]);
}

/**
@template






*/
public static function times($number, ?callable $callback = null)
{
if ($number < 1) {
return new static;
}

return static::range(1, $number)
->unless($callback == null)
->map($callback);
}









public static function fromJson($json, $depth = 512, $flags = 0)
{
return new static(json_decode($json, true, $depth, $flags));
}







public function avg($callback = null)
{
$callback = $this->valueRetriever($callback);

$reduced = $this->reduce(static function (&$reduce, $value) use ($callback) {
if (! is_null($resolved = $callback($value))) {
$reduce[0] += $resolved;
$reduce[1]++;
}

return $reduce;
}, [0, 0]);

return $reduced[1] ? $reduced[0] / $reduced[1] : null;
}







public function average($callback = null)
{
return $this->avg($callback);
}









public function some($key, $operator = null, $value = null)
{
return $this->contains(...func_get_args());
}







public function dd(...$args)
{
dd($this->all(), ...$args);
}







public function dump(...$args)
{
dump($this->all(), ...$args);

return $this;
}







public function each(callable $callback)
{
foreach ($this as $key => $item) {
if ($callback($item, $key) === false) {
break;
}
}

return $this;
}







public function eachSpread(callable $callback)
{
return $this->each(function ($chunk, $key) use ($callback) {
$chunk[] = $key;

return $callback(...$chunk);
});
}









public function every($key, $operator = null, $value = null)
{
if (func_num_args() === 1) {
$callback = $this->valueRetriever($key);

foreach ($this as $k => $v) {
if (! $callback($v, $k)) {
return false;
}
}

return true;
}

return $this->every($this->operatorForWhere(...func_get_args()));
}









public function firstWhere($key, $operator = null, $value = null)
{
return $this->first($this->operatorForWhere(...func_get_args()));
}

/**
@template






*/
public function value($key, $default = null)
{
if ($value = $this->firstWhere($key)) {
return data_get($value, $key, $default);
}

return value($default);
}

/**
@template







*/
public function ensure($type)
{
$allowedTypes = is_array($type) ? $type : [$type];

return $this->each(function ($item, $index) use ($allowedTypes) {
$itemType = get_debug_type($item);

foreach ($allowedTypes as $allowedType) {
if ($itemType === $allowedType || $item instanceof $allowedType) {
return true;
}
}

throw new UnexpectedValueException(
sprintf("Collection should only include [%s] items, but '%s' found at position %d.", implode(', ', $allowedTypes), $itemType, $index)
);
});
}

/**
@phpstan-assert-if-true
@phpstan-assert-if-true
@phpstan-assert-if-false
@phpstan-assert-if-false





*/
public function isNotEmpty()
{
return ! $this->isEmpty();
}

/**
@template





*/
public function mapSpread(callable $callback)
{
return $this->map(function ($chunk, $key) use ($callback) {
$chunk[] = $key;

return $callback(...$chunk);
});
}

/**
@template
@template







*/
public function mapToGroups(callable $callback)
{
$groups = $this->mapToDictionary($callback);

return $groups->map($this->make(...));
}

/**
@template
@template





*/
public function flatMap(callable $callback)
{
return $this->map($callback)->collapse();
}

/**
@template





*/
public function mapInto($class)
{
if (is_subclass_of($class, BackedEnum::class)) {
return $this->map(fn ($value, $key) => $class::from($value));
}

return $this->map(fn ($value, $key) => new $class($value, $key));
}







public function min($callback = null)
{
$callback = $this->valueRetriever($callback);

return $this->map(fn ($value) => $callback($value))
->reject(fn ($value) => is_null($value))
->reduce(fn ($result, $value) => is_null($result) || $value < $result ? $value : $result);
}







public function max($callback = null)
{
$callback = $this->valueRetriever($callback);

return $this->reject(fn ($value) => is_null($value))->reduce(function ($result, $item) use ($callback) {
$value = $callback($item);

return is_null($result) || $value > $result ? $value : $result;
});
}








public function forPage($page, $perPage)
{
$offset = max(0, ($page - 1) * $perPage);

return $this->slice($offset, $perPage);
}









public function partition($key, $operator = null, $value = null)
{
$callback = func_num_args() === 1
? $this->valueRetriever($key)
: $this->operatorForWhere(...func_get_args());

[$passed, $failed] = Arr::partition($this->getIterator(), $callback);

return new static([new static($passed), new static($failed)]);
}








public function percentage(callable $callback, int $precision = 2)
{
if ($this->isEmpty()) {
return null;
}

return round(
$this->filter($callback)->count() / $this->count() * 100,
$precision
);
}

/**
@template





*/
public function sum($callback = null)
{
$callback = is_null($callback)
? $this->identity()
: $this->valueRetriever($callback);

return $this->reduce(fn ($result, $item) => $result + $callback($item), 0);
}

/**
@template






*/
public function whenEmpty(callable $callback, ?callable $default = null)
{
return $this->when($this->isEmpty(), $callback, $default);
}

/**
@template






*/
public function whenNotEmpty(callable $callback, ?callable $default = null)
{
return $this->when($this->isNotEmpty(), $callback, $default);
}

/**
@template






*/
public function unlessEmpty(callable $callback, ?callable $default = null)
{
return $this->whenNotEmpty($callback, $default);
}

/**
@template






*/
public function unlessNotEmpty(callable $callback, ?callable $default = null)
{
return $this->whenEmpty($callback, $default);
}









public function where($key, $operator = null, $value = null)
{
return $this->filter($this->operatorForWhere(...func_get_args()));
}







public function whereNull($key = null)
{
return $this->whereStrict($key, null);
}







public function whereNotNull($key = null)
{
return $this->where($key, '!==', null);
}








public function whereStrict($key, $value)
{
return $this->where($key, '===', $value);
}









public function whereIn($key, $values, $strict = false)
{
$values = $this->getArrayableItems($values);

return $this->filter(fn ($item) => in_array(data_get($item, $key), $values, $strict));
}








public function whereInStrict($key, $values)
{
return $this->whereIn($key, $values, true);
}








public function whereBetween($key, $values)
{
return $this->where($key, '>=', reset($values))->where($key, '<=', end($values));
}








public function whereNotBetween($key, $values)
{
return $this->filter(
fn ($item) => data_get($item, $key) < reset($values) || data_get($item, $key) > end($values)
);
}









public function whereNotIn($key, $values, $strict = false)
{
$values = $this->getArrayableItems($values);

return $this->reject(fn ($item) => in_array(data_get($item, $key), $values, $strict));
}








public function whereNotInStrict($key, $values)
{
return $this->whereNotIn($key, $values, true);
}

/**
@template





*/
public function whereInstanceOf($type)
{
return $this->filter(function ($value) use ($type) {
if (is_array($type)) {
foreach ($type as $classType) {
if ($value instanceof $classType) {
return true;
}
}

return false;
}

return $value instanceof $type;
});
}

/**
@template





*/
public function pipe(callable $callback)
{
return $callback($this);
}

/**
@template





*/
public function pipeInto($class)
{
return new $class($this);
}







public function pipeThrough($callbacks)
{
return (new Collection($callbacks))->reduce(
fn ($carry, $callback) => $callback($carry),
$this,
);
}

/**
@template
@template






*/
public function reduce(callable $callback, $initial = null)
{
$result = $initial;

foreach ($this as $key => $value) {
$result = $callback($result, $value, $key);
}

return $result;
}










public function reduceSpread(callable $callback, ...$initial)
{
$result = $initial;

foreach ($this as $key => $value) {
$result = call_user_func_array($callback, array_merge($result, [$value, $key]));

if (! is_array($result)) {
throw new UnexpectedValueException(sprintf(
"%s::reduceSpread expects reducer to return an array, but got a '%s' instead.",
class_basename(static::class), gettype($result)
));
}
}

return $result;
}

/**
@template
@template






*/
public function reduceWithKeys(callable $callback, $initial = null)
{
return $this->reduce($callback, $initial);
}







public function reject($callback = true)
{
$useAsCallable = $this->useAsCallable($callback);

return $this->filter(function ($value, $key) use ($callback, $useAsCallable) {
return $useAsCallable
? ! $callback($value, $key)
: $value != $callback;
});
}







public function tap(callable $callback)
{
$callback($this);

return $this;
}








public function unique($key = null, $strict = false)
{
$callback = $this->valueRetriever($key);

$exists = [];

return $this->reject(function ($item, $key) use ($callback, $strict, &$exists) {
if (in_array($id = $callback($item, $key), $exists, $strict)) {
return true;
}

$exists[] = $id;
});
}







public function uniqueStrict($key = null)
{
return $this->unique($key, true);
}






public function collect()
{
return new Collection($this->all());
}






public function toArray()
{
return $this->map(fn ($value) => $value instanceof Arrayable ? $value->toArray() : $value)->all();
}






public function jsonSerialize(): array
{
return array_map(function ($value) {
if ($value instanceof JsonSerializable) {
return $value->jsonSerialize();
} elseif ($value instanceof Jsonable) {
return json_decode($value->toJson(), true);
} elseif ($value instanceof Arrayable) {
return $value->toArray();
}

return $value;
}, $this->all());
}







public function toJson($options = 0)
{
return json_encode($this->jsonSerialize(), $options);
}







public function getCachingIterator($flags = CachingIterator::CALL_TOSTRING)
{
return new CachingIterator($this->getIterator(), $flags);
}






public function __toString()
{
return $this->escapeWhenCastingToString
? e($this->toJson())
: $this->toJson();
}







public function escapeWhenCastingToString($escape = true)
{
$this->escapeWhenCastingToString = $escape;

return $this;
}







public static function proxy($method)
{
static::$proxies[] = $method;
}









public function __get($key)
{
if (! in_array($key, static::$proxies)) {
throw new Exception("Property [{$key}] does not exist on this collection instance.");
}

return new HigherOrderCollectionProxy($this, $key);
}







protected function getArrayableItems($items)
{
return is_null($items) || is_scalar($items) || $items instanceof UnitEnum
? Arr::wrap($items)
: Arr::from($items);
}









protected function operatorForWhere($key, $operator = null, $value = null)
{
if ($this->useAsCallable($key)) {
return $key;
}

if (func_num_args() === 1) {
$value = true;

$operator = '=';
}

if (func_num_args() === 2) {
$value = $operator;

$operator = '=';
}

return function ($item) use ($key, $operator, $value) {
$retrieved = enum_value(data_get($item, $key));
$value = enum_value($value);

$strings = array_filter([$retrieved, $value], function ($value) {
return match (true) {
is_string($value) => true,
$value instanceof \Stringable => true,
default => false,
};
});

if (count($strings) < 2 && count(array_filter([$retrieved, $value], 'is_object')) == 1) {
return in_array($operator, ['!=', '<>', '!==']);
}

switch ($operator) {
default:
case '=':
case '==': return $retrieved == $value;
case '!=':
case '<>': return $retrieved != $value;
case '<': return $retrieved < $value;
case '>': return $retrieved > $value;
case '<=': return $retrieved <= $value;
case '>=': return $retrieved >= $value;
case '===': return $retrieved === $value;
case '!==': return $retrieved !== $value;
case '<=>': return $retrieved <=> $value;
}
};
}







protected function useAsCallable($value)
{
return ! is_string($value) && is_callable($value);
}







protected function valueRetriever($value)
{
if ($this->useAsCallable($value)) {
return $value;
}

return fn ($item) => data_get($item, $value);
}







protected function equality($value)
{
return fn ($item) => $item === $value;
}







protected function negate(Closure $callback)
{
return fn (...$params) => ! $callback(...$params);
}






protected function identity()
{
return fn ($value) => $value;
}
}
