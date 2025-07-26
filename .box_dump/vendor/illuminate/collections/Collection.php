<?php

namespace Illuminate\Support;

use ArrayAccess;
use ArrayIterator;
use Illuminate\Contracts\Support\CanBeEscapedWhenCastToString;
use Illuminate\Support\Traits\EnumeratesValues;
use Illuminate\Support\Traits\Macroable;
use Illuminate\Support\Traits\TransformsToResourceCollection;
use InvalidArgumentException;
use stdClass;
use Traversable;

/**
@template
@template-covariant
@implements
@implements


*/
class Collection implements ArrayAccess, CanBeEscapedWhenCastToString, Enumerable
{
/**
@use
*/
use EnumeratesValues, Macroable, TransformsToResourceCollection;






protected $items = [];






public function __construct($items = [])
{
$this->items = $this->getArrayableItems($items);
}









public static function range($from, $to, $step = 1)
{
return new static(range($from, $to, $step));
}






public function all()
{
return $this->items;
}






public function lazy()
{
return new LazyCollection($this->items);
}







public function median($key = null)
{
$values = (isset($key) ? $this->pluck($key) : $this)
->reject(fn ($item) => is_null($item))
->sort()->values();

$count = $values->count();

if ($count === 0) {
return;
}

$middle = (int) ($count / 2);

if ($count % 2) {
return $values->get($middle);
}

return (new static([
$values->get($middle - 1), $values->get($middle),
]))->average();
}







public function mode($key = null)
{
if ($this->count() === 0) {
return;
}

$collection = isset($key) ? $this->pluck($key) : $this;

$counts = new static;

$collection->each(fn ($value) => $counts[$value] = isset($counts[$value]) ? $counts[$value] + 1 : 1);

$sorted = $counts->sort();

$highestValue = $sorted->last();

return $sorted->filter(fn ($value) => $value == $highestValue)
->sort()->keys()->all();
}






public function collapse()
{
return new static(Arr::collapse($this->items));
}






public function collapseWithKeys()
{
if (! $this->items) {
return new static;
}

$results = [];

foreach ($this->items as $key => $values) {
if ($values instanceof Collection) {
$values = $values->all();
} elseif (! is_array($values)) {
continue;
}

$results[$key] = $values;
}

if (! $results) {
return new static;
}

return new static(array_replace(...$results));
}









public function contains($key, $operator = null, $value = null)
{
if (func_num_args() === 1) {
if ($this->useAsCallable($key)) {
$placeholder = new stdClass;

return $this->first($key, $placeholder) !== $placeholder;
}

return in_array($key, $this->items);
}

return $this->contains($this->operatorForWhere(...func_get_args()));
}








public function containsStrict($key, $value = null)
{
if (func_num_args() === 2) {
return $this->contains(fn ($item) => data_get($item, $key) === $value);
}

if ($this->useAsCallable($key)) {
return ! is_null($this->first($key));
}

return in_array($key, $this->items, true);
}









public function doesntContain($key, $operator = null, $value = null)
{
return ! $this->contains(...func_get_args());
}

/**
@template
@template





*/
public function crossJoin(...$lists)
{
return new static(Arr::crossJoin(
$this->items, ...array_map($this->getArrayableItems(...), $lists)
));
}







public function diff($items)
{
return new static(array_diff($this->items, $this->getArrayableItems($items)));
}








public function diffUsing($items, callable $callback)
{
return new static(array_udiff($this->items, $this->getArrayableItems($items), $callback));
}







public function diffAssoc($items)
{
return new static(array_diff_assoc($this->items, $this->getArrayableItems($items)));
}








public function diffAssocUsing($items, callable $callback)
{
return new static(array_diff_uassoc($this->items, $this->getArrayableItems($items), $callback));
}







public function diffKeys($items)
{
return new static(array_diff_key($this->items, $this->getArrayableItems($items)));
}








public function diffKeysUsing($items, callable $callback)
{
return new static(array_diff_ukey($this->items, $this->getArrayableItems($items), $callback));
}

/**
@template






*/
public function duplicates($callback = null, $strict = false)
{
$items = $this->map($this->valueRetriever($callback));

$uniqueItems = $items->unique(null, $strict);

$compare = $this->duplicateComparator($strict);

$duplicates = new static;

foreach ($items as $key => $value) {
if ($uniqueItems->isNotEmpty() && $compare($value, $uniqueItems->first())) {
$uniqueItems->shift();
} else {
$duplicates[$key] = $value;
}
}

return $duplicates;
}

/**
@template





*/
public function duplicatesStrict($callback = null)
{
return $this->duplicates($callback, true);
}







protected function duplicateComparator($strict)
{
if ($strict) {
return fn ($a, $b) => $a === $b;
}

return fn ($a, $b) => $a == $b;
}







public function except($keys)
{
if (is_null($keys)) {
return new static($this->items);
}

if ($keys instanceof Enumerable) {
$keys = $keys->all();
} elseif (! is_array($keys)) {
$keys = func_get_args();
}

return new static(Arr::except($this->items, $keys));
}







public function filter(?callable $callback = null)
{
if ($callback) {
return new static(Arr::where($this->items, $callback));
}

return new static(array_filter($this->items));
}

/**
@template






*/
public function first(?callable $callback = null, $default = null)
{
return Arr::first($this->items, $callback, $default);
}







public function flatten($depth = INF)
{
return new static(Arr::flatten($this->items, $depth));
}






public function flip()
{
return new static(array_flip($this->items));
}








public function forget($keys)
{
foreach ($this->getArrayableItems($keys) as $key) {
$this->offsetUnset($key);
}

return $this;
}

/**
@template






*/
public function get($key, $default = null)
{
if (array_key_exists($key, $this->items)) {
return $this->items[$key];
}

return value($default);
}

/**
@template






*/
public function getOrPut($key, $value)
{
if (array_key_exists($key, $this->items)) {
return $this->items[$key];
}

$this->offsetSet($key, $value = value($value));

return $value;
}

/**
@template






*/
public function groupBy($groupBy, $preserveKeys = false)
{
if (! $this->useAsCallable($groupBy) && is_array($groupBy)) {
$nextGroups = $groupBy;

$groupBy = array_shift($nextGroups);
}

$groupBy = $this->valueRetriever($groupBy);

$results = [];

foreach ($this->items as $key => $value) {
$groupKeys = $groupBy($value, $key);

if (! is_array($groupKeys)) {
$groupKeys = [$groupKeys];
}

foreach ($groupKeys as $groupKey) {
$groupKey = match (true) {
is_bool($groupKey) => (int) $groupKey,
$groupKey instanceof \BackedEnum => $groupKey->value,
$groupKey instanceof \Stringable => (string) $groupKey,
default => $groupKey,
};

if (! array_key_exists($groupKey, $results)) {
$results[$groupKey] = new static;
}

$results[$groupKey]->offsetSet($preserveKeys ? $key : null, $value);
}
}

$result = new static($results);

if (! empty($nextGroups)) {
return $result->map->groupBy($nextGroups, $preserveKeys);
}

return $result;
}

/**
@template





*/
public function keyBy($keyBy)
{
$keyBy = $this->valueRetriever($keyBy);

$results = [];

foreach ($this->items as $key => $item) {
$resolvedKey = $keyBy($item, $key);

if (is_object($resolvedKey)) {
$resolvedKey = (string) $resolvedKey;
}

$results[$resolvedKey] = $item;
}

return new static($results);
}







public function has($key)
{
$keys = is_array($key) ? $key : func_get_args();

foreach ($keys as $value) {
if (! array_key_exists($value, $this->items)) {
return false;
}
}

return true;
}







public function hasAny($key)
{
if ($this->isEmpty()) {
return false;
}

$keys = is_array($key) ? $key : func_get_args();

foreach ($keys as $value) {
if (array_key_exists($value, $this->items)) {
return true;
}
}

return false;
}








public function implode($value, $glue = null)
{
if ($this->useAsCallable($value)) {
return implode($glue ?? '', $this->map($value)->all());
}

$first = $this->first();

if (is_array($first) || (is_object($first) && ! $first instanceof Stringable)) {
return implode($glue ?? '', $this->pluck($value)->all());
}

return implode($value ?? '', $this->items);
}







public function intersect($items)
{
return new static(array_intersect($this->items, $this->getArrayableItems($items)));
}








public function intersectUsing($items, callable $callback)
{
return new static(array_uintersect($this->items, $this->getArrayableItems($items), $callback));
}







public function intersectAssoc($items)
{
return new static(array_intersect_assoc($this->items, $this->getArrayableItems($items)));
}








public function intersectAssocUsing($items, callable $callback)
{
return new static(array_intersect_uassoc($this->items, $this->getArrayableItems($items), $callback));
}







public function intersectByKeys($items)
{
return new static(array_intersect_key(
$this->items, $this->getArrayableItems($items)
));
}

/**
@phpstan-assert-if-true
@phpstan-assert-if-true
@phpstan-assert-if-false
@phpstan-assert-if-false





*/
public function isEmpty()
{
return empty($this->items);
}







public function containsOneItem(?callable $callback = null): bool
{
if ($callback) {
return $this->filter($callback)->count() === 1;
}

return $this->count() === 1;
}








public function join($glue, $finalGlue = '')
{
if ($finalGlue === '') {
return $this->implode($glue);
}

$count = $this->count();

if ($count === 0) {
return '';
}

if ($count === 1) {
return $this->last();
}

$collection = new static($this->items);

$finalItem = $collection->pop();

return $collection->implode($glue).$finalGlue.$finalItem;
}






public function keys()
{
return new static(array_keys($this->items));
}

/**
@template






*/
public function last(?callable $callback = null, $default = null)
{
return Arr::last($this->items, $callback, $default);
}








public function pluck($value, $key = null)
{
return new static(Arr::pluck($this->items, $value, $key));
}

/**
@template





*/
public function map(callable $callback)
{
return new static(Arr::map($this->items, $callback));
}

/**
@template
@template







*/
public function mapToDictionary(callable $callback)
{
$dictionary = [];

foreach ($this->items as $key => $item) {
$pair = $callback($item, $key);

$key = key($pair);

$value = reset($pair);

if (! isset($dictionary[$key])) {
$dictionary[$key] = [];
}

$dictionary[$key][] = $value;
}

return new static($dictionary);
}

/**
@template
@template







*/
public function mapWithKeys(callable $callback)
{
return new static(Arr::mapWithKeys($this->items, $callback));
}







public function merge($items)
{
return new static(array_merge($this->items, $this->getArrayableItems($items)));
}

/**
@template





*/
public function mergeRecursive($items)
{
return new static(array_merge_recursive($this->items, $this->getArrayableItems($items)));
}







public function multiply(int $multiplier)
{
$new = new static;

for ($i = 0; $i < $multiplier; $i++) {
$new->push(...$this->items);
}

return $new;
}

/**
@template





*/
public function combine($values)
{
return new static(array_combine($this->all(), $this->getArrayableItems($values)));
}







public function union($items)
{
return new static($this->items + $this->getArrayableItems($items));
}








public function nth($step, $offset = 0)
{
$new = [];

$position = 0;

foreach ($this->slice($offset)->items as $item) {
if ($position % $step === 0) {
$new[] = $item;
}

$position++;
}

return new static($new);
}







public function only($keys)
{
if (is_null($keys)) {
return new static($this->items);
}

if ($keys instanceof Enumerable) {
$keys = $keys->all();
}

$keys = is_array($keys) ? $keys : func_get_args();

return new static(Arr::only($this->items, $keys));
}







public function select($keys)
{
if (is_null($keys)) {
return new static($this->items);
}

if ($keys instanceof Enumerable) {
$keys = $keys->all();
}

$keys = is_array($keys) ? $keys : func_get_args();

return new static(Arr::select($this->items, $keys));
}







public function pop($count = 1)
{
if ($count < 1) {
return new static;
}

if ($count === 1) {
return array_pop($this->items);
}

if ($this->isEmpty()) {
return new static;
}

$results = [];

$collectionCount = $this->count();

foreach (range(1, min($count, $collectionCount)) as $item) {
$results[] = array_pop($this->items);
}

return new static($results);
}








public function prepend($value, $key = null)
{
$this->items = Arr::prepend($this->items, ...func_get_args());

return $this;
}







public function push(...$values)
{
foreach ($values as $value) {
$this->items[] = $value;
}

return $this;
}







public function unshift(...$values)
{
array_unshift($this->items, ...$values);

return $this;
}

/**
@template
@template





*/
public function concat($source)
{
$result = new static($this);

foreach ($source as $item) {
$result->push($item);
}

return $result;
}

/**
@template






*/
public function pull($key, $default = null)
{
return Arr::pull($this->items, $key, $default);
}








public function put($key, $value)
{
$this->offsetSet($key, $value);

return $this;
}










public function random($number = null, $preserveKeys = false)
{
if (is_null($number)) {
return Arr::random($this->items);
}

if (is_callable($number)) {
return new static(Arr::random($this->items, $number($this), $preserveKeys));
}

return new static(Arr::random($this->items, $number, $preserveKeys));
}







public function replace($items)
{
return new static(array_replace($this->items, $this->getArrayableItems($items)));
}







public function replaceRecursive($items)
{
return new static(array_replace_recursive($this->items, $this->getArrayableItems($items)));
}






public function reverse()
{
return new static(array_reverse($this->items, true));
}








public function search($value, $strict = false)
{
if (! $this->useAsCallable($value)) {
return array_search($value, $this->items, $strict);
}

foreach ($this->items as $key => $item) {
if ($value($item, $key)) {
return $key;
}
}

return false;
}








public function before($value, $strict = false)
{
$key = $this->search($value, $strict);

if ($key === false) {
return null;
}

$position = ($keys = $this->keys())->search($key);

if ($position === 0) {
return null;
}

return $this->get($keys->get($position - 1));
}








public function after($value, $strict = false)
{
$key = $this->search($value, $strict);

if ($key === false) {
return null;
}

$position = ($keys = $this->keys())->search($key);

if ($position === $keys->count() - 1) {
return null;
}

return $this->get($keys->get($position + 1));
}









public function shift($count = 1)
{
if ($count < 0) {
throw new InvalidArgumentException('Number of shifted items may not be less than zero.');
}

if ($this->isEmpty()) {
return null;
}

if ($count === 0) {
return new static;
}

if ($count === 1) {
return array_shift($this->items);
}

$results = [];

$collectionCount = $this->count();

foreach (range(1, min($count, $collectionCount)) as $item) {
$results[] = array_shift($this->items);
}

return new static($results);
}






public function shuffle()
{
return new static(Arr::shuffle($this->items));
}








public function sliding($size = 2, $step = 1)
{
$chunks = floor(($this->count() - $size) / $step) + 1;

return static::times($chunks, fn ($number) => $this->slice(($number - 1) * $step, $size));
}







public function skip($count)
{
return $this->slice($count);
}







public function skipUntil($value)
{
return new static($this->lazy()->skipUntil($value)->all());
}







public function skipWhile($value)
{
return new static($this->lazy()->skipWhile($value)->all());
}








public function slice($offset, $length = null)
{
return new static(array_slice($this->items, $offset, $length, true));
}







public function split($numberOfGroups)
{
if ($this->isEmpty()) {
return new static;
}

$groups = new static;

$groupSize = floor($this->count() / $numberOfGroups);

$remain = $this->count() % $numberOfGroups;

$start = 0;

for ($i = 0; $i < $numberOfGroups; $i++) {
$size = $groupSize;

if ($i < $remain) {
$size++;
}

if ($size) {
$groups->push(new static(array_slice($this->items, $start, $size)));

$start += $size;
}
}

return $groups;
}







public function splitIn($numberOfGroups)
{
return $this->chunk((int) ceil($this->count() / $numberOfGroups));
}












public function sole($key = null, $operator = null, $value = null)
{
$filter = func_num_args() > 1
? $this->operatorForWhere(...func_get_args())
: $key;

$items = $this->unless($filter == null)->filter($filter);

$count = $items->count();

if ($count === 0) {
throw new ItemNotFoundException;
}

if ($count > 1) {
throw new MultipleItemsFoundException($count);
}

return $items->first();
}











public function firstOrFail($key = null, $operator = null, $value = null)
{
$filter = func_num_args() > 1
? $this->operatorForWhere(...func_get_args())
: $key;

$placeholder = new stdClass();

$item = $this->first($filter, $placeholder);

if ($item === $placeholder) {
throw new ItemNotFoundException;
}

return $item;
}








public function chunk($size, $preserveKeys = true)
{
if ($size <= 0) {
return new static;
}

$chunks = [];

foreach (array_chunk($this->items, $size, $preserveKeys) as $chunk) {
$chunks[] = new static($chunk);
}

return new static($chunks);
}







public function chunkWhile(callable $callback)
{
return new static(
$this->lazy()->chunkWhile($callback)->mapInto(static::class)
);
}







public function sort($callback = null)
{
$items = $this->items;

$callback && is_callable($callback)
? uasort($items, $callback)
: asort($items, $callback ?? SORT_REGULAR);

return new static($items);
}







public function sortDesc($options = SORT_REGULAR)
{
$items = $this->items;

arsort($items, $options);

return new static($items);
}









public function sortBy($callback, $options = SORT_REGULAR, $descending = false)
{
if (is_array($callback) && ! is_callable($callback)) {
return $this->sortByMany($callback, $options);
}

$results = [];

$callback = $this->valueRetriever($callback);




foreach ($this->items as $key => $value) {
$results[$key] = $callback($value, $key);
}

$descending ? arsort($results, $options)
: asort($results, $options);




foreach (array_keys($results) as $key) {
$results[$key] = $this->items[$key];
}

return new static($results);
}








protected function sortByMany(array $comparisons = [], int $options = SORT_REGULAR)
{
$items = $this->items;

uasort($items, function ($a, $b) use ($comparisons, $options) {
foreach ($comparisons as $comparison) {
$comparison = Arr::wrap($comparison);

$prop = $comparison[0];

$ascending = Arr::get($comparison, 1, true) === true ||
Arr::get($comparison, 1, true) === 'asc';

if (! is_string($prop) && is_callable($prop)) {
$result = $prop($a, $b);
} else {
$values = [data_get($a, $prop), data_get($b, $prop)];

if (! $ascending) {
$values = array_reverse($values);
}

if (($options & SORT_FLAG_CASE) === SORT_FLAG_CASE) {
if (($options & SORT_NATURAL) === SORT_NATURAL) {
$result = strnatcasecmp($values[0], $values[1]);
} else {
$result = strcasecmp($values[0], $values[1]);
}
} else {
$result = match ($options) {
SORT_NUMERIC => intval($values[0]) <=> intval($values[1]),
SORT_STRING => strcmp($values[0], $values[1]),
SORT_NATURAL => strnatcmp((string) $values[0], (string) $values[1]),
SORT_LOCALE_STRING => strcoll($values[0], $values[1]),
default => $values[0] <=> $values[1],
};
}
}

if ($result === 0) {
continue;
}

return $result;
}
});

return new static($items);
}








public function sortByDesc($callback, $options = SORT_REGULAR)
{
if (is_array($callback) && ! is_callable($callback)) {
foreach ($callback as $index => $key) {
$comparison = Arr::wrap($key);

$comparison[1] = 'desc';

$callback[$index] = $comparison;
}
}

return $this->sortBy($callback, $options, true);
}








public function sortKeys($options = SORT_REGULAR, $descending = false)
{
$items = $this->items;

$descending ? krsort($items, $options) : ksort($items, $options);

return new static($items);
}







public function sortKeysDesc($options = SORT_REGULAR)
{
return $this->sortKeys($options, true);
}







public function sortKeysUsing(callable $callback)
{
$items = $this->items;

uksort($items, $callback);

return new static($items);
}









public function splice($offset, $length = null, $replacement = [])
{
if (func_num_args() === 1) {
return new static(array_splice($this->items, $offset));
}

return new static(array_splice($this->items, $offset, $length, $this->getArrayableItems($replacement)));
}







public function take($limit)
{
if ($limit < 0) {
return $this->slice($limit, abs($limit));
}

return $this->slice(0, $limit);
}







public function takeUntil($value)
{
return new static($this->lazy()->takeUntil($value)->all());
}







public function takeWhile($value)
{
return new static($this->lazy()->takeWhile($value)->all());
}

/**
@template
@phpstan-this-out






*/
public function transform(callable $callback)
{
$this->items = $this->map($callback)->all();

return $this;
}






public function dot()
{
return new static(Arr::dot($this->all()));
}






public function undot()
{
return new static(Arr::undot($this->all()));
}








public function unique($key = null, $strict = false)
{
if (is_null($key) && $strict === false) {
return new static(array_unique($this->items, SORT_REGULAR));
}

$callback = $this->valueRetriever($key);

$exists = [];

return $this->reject(function ($item, $key) use ($callback, $strict, &$exists) {
if (in_array($id = $callback($item, $key), $exists, $strict)) {
return true;
}

$exists[] = $id;
});
}






public function values()
{
return new static(array_values($this->items));
}

/**
@template








*/
public function zip($items)
{
$arrayableItems = array_map(fn ($items) => $this->getArrayableItems($items), func_get_args());

$params = array_merge([fn () => new static(func_get_args()), $this->items], $arrayableItems);

return new static(array_map(...$params));
}

/**
@template






*/
public function pad($size, $value)
{
return new static(array_pad($this->items, $size, $value));
}






public function getIterator(): Traversable
{
return new ArrayIterator($this->items);
}






public function count(): int
{
return count($this->items);
}







public function countBy($countBy = null)
{
return new static($this->lazy()->countBy($countBy)->all());
}







public function add($item)
{
$this->items[] = $item;

return $this;
}






public function toBase()
{
return new self($this);
}







public function offsetExists($key): bool
{
return isset($this->items[$key]);
}







public function offsetGet($key): mixed
{
return $this->items[$key];
}








public function offsetSet($key, $value): void
{
if (is_null($key)) {
$this->items[] = $value;
} else {
$this->items[$key] = $value;
}
}







public function offsetUnset($key): void
{
unset($this->items[$key]);
}
}
