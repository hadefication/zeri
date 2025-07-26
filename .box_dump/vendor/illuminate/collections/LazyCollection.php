<?php

namespace Illuminate\Support;

use ArrayIterator;
use Closure;
use DateTimeInterface;
use Generator;
use Illuminate\Contracts\Support\CanBeEscapedWhenCastToString;
use Illuminate\Support\Traits\EnumeratesValues;
use Illuminate\Support\Traits\Macroable;
use InvalidArgumentException;
use IteratorAggregate;
use stdClass;
use Traversable;

/**
@template
@template-covariant
@implements


*/
class LazyCollection implements CanBeEscapedWhenCastToString, Enumerable
{
/**
@use
*/
use EnumeratesValues, Macroable;






public $source;






public function __construct($source = null)
{
if ($source instanceof Closure || $source instanceof self) {
$this->source = $source;
} elseif (is_null($source)) {
$this->source = static::empty();
} elseif ($source instanceof Generator) {
throw new InvalidArgumentException(
'Generators should not be passed directly to LazyCollection. Instead, pass a generator function.'
);
} else {
$this->source = $this->getArrayableItems($source);
}
}

/**
@template
@template





*/
public static function make($items = [])
{
return new static($items);
}









public static function range($from, $to, $step = 1)
{
if ($step == 0) {
throw new InvalidArgumentException('Step value cannot be zero.');
}

return new static(function () use ($from, $to, $step) {
if ($from <= $to) {
for (; $from <= $to; $from += abs($step)) {
yield $from;
}
} else {
for (; $from >= $to; $from -= abs($step)) {
yield $from;
}
}
});
}






public function all()
{
if (is_array($this->source)) {
return $this->source;
}

return iterator_to_array($this->getIterator());
}






public function eager()
{
return new static($this->all());
}






public function remember()
{
$iterator = $this->getIterator();

$iteratorIndex = 0;

$cache = [];

return new static(function () use ($iterator, &$iteratorIndex, &$cache) {
for ($index = 0; true; $index++) {
if (array_key_exists($index, $cache)) {
yield $cache[$index][0] => $cache[$index][1];

continue;
}

if ($iteratorIndex < $index) {
$iterator->next();

$iteratorIndex++;
}

if (! $iterator->valid()) {
break;
}

$cache[$index] = [$iterator->key(), $iterator->current()];

yield $cache[$index][0] => $cache[$index][1];
}
});
}







public function median($key = null)
{
return $this->collect()->median($key);
}







public function mode($key = null)
{
return $this->collect()->mode($key);
}






public function collapse()
{
return new static(function () {
foreach ($this as $values) {
if (is_array($values) || $values instanceof Enumerable) {
foreach ($values as $value) {
yield $value;
}
}
}
});
}






public function collapseWithKeys()
{
return new static(function () {
foreach ($this as $values) {
if (is_array($values) || $values instanceof Enumerable) {
foreach ($values as $key => $value) {
yield $key => $value;
}
}
}
});
}









public function contains($key, $operator = null, $value = null)
{
if (func_num_args() === 1 && $this->useAsCallable($key)) {
$placeholder = new stdClass;


return $this->first($key, $placeholder) !== $placeholder;
}

if (func_num_args() === 1) {
$needle = $key;

foreach ($this as $value) {
if ($value == $needle) {
return true;
}
}

return false;
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

foreach ($this as $item) {
if ($item === $key) {
return true;
}
}

return false;
}









public function doesntContain($key, $operator = null, $value = null)
{
return ! $this->contains(...func_get_args());
}

/**
@template
@template





*/
public function crossJoin(...$arrays)
{
return $this->passthru('crossJoin', func_get_args());
}







public function countBy($countBy = null)
{
$countBy = is_null($countBy)
? $this->identity()
: $this->valueRetriever($countBy);

return new static(function () use ($countBy) {
$counts = [];

foreach ($this as $key => $value) {
$group = $countBy($value, $key);

if (empty($counts[$group])) {
$counts[$group] = 0;
}

$counts[$group]++;
}

yield from $counts;
});
}







public function diff($items)
{
return $this->passthru('diff', func_get_args());
}








public function diffUsing($items, callable $callback)
{
return $this->passthru('diffUsing', func_get_args());
}







public function diffAssoc($items)
{
return $this->passthru('diffAssoc', func_get_args());
}








public function diffAssocUsing($items, callable $callback)
{
return $this->passthru('diffAssocUsing', func_get_args());
}







public function diffKeys($items)
{
return $this->passthru('diffKeys', func_get_args());
}








public function diffKeysUsing($items, callable $callback)
{
return $this->passthru('diffKeysUsing', func_get_args());
}

/**
@template






*/
public function duplicates($callback = null, $strict = false)
{
return $this->passthru('duplicates', func_get_args());
}

/**
@template





*/
public function duplicatesStrict($callback = null)
{
return $this->passthru('duplicatesStrict', func_get_args());
}







public function except($keys)
{
return $this->passthru('except', func_get_args());
}







public function filter(?callable $callback = null)
{
if (is_null($callback)) {
$callback = fn ($value) => (bool) $value;
}

return new static(function () use ($callback) {
foreach ($this as $key => $value) {
if ($callback($value, $key)) {
yield $key => $value;
}
}
});
}

/**
@template






*/
public function first(?callable $callback = null, $default = null)
{
$iterator = $this->getIterator();

if (is_null($callback)) {
if (! $iterator->valid()) {
return value($default);
}

return $iterator->current();
}

foreach ($iterator as $key => $value) {
if ($callback($value, $key)) {
return $value;
}
}

return value($default);
}







public function flatten($depth = INF)
{
$instance = new static(function () use ($depth) {
foreach ($this as $item) {
if (! is_array($item) && ! $item instanceof Enumerable) {
yield $item;
} elseif ($depth === 1) {
yield from $item;
} else {
yield from (new static($item))->flatten($depth - 1);
}
}
});

return $instance->values();
}






public function flip()
{
return new static(function () {
foreach ($this as $key => $value) {
yield $value => $key;
}
});
}

/**
@template






*/
public function get($key, $default = null)
{
if (is_null($key)) {
return;
}

foreach ($this as $outerKey => $outerValue) {
if ($outerKey == $key) {
return $outerValue;
}
}

return value($default);
}

/**
@template






*/
public function groupBy($groupBy, $preserveKeys = false)
{
return $this->passthru('groupBy', func_get_args());
}

/**
@template





*/
public function keyBy($keyBy)
{
return new static(function () use ($keyBy) {
$keyBy = $this->valueRetriever($keyBy);

foreach ($this as $key => $item) {
$resolvedKey = $keyBy($item, $key);

if (is_object($resolvedKey)) {
$resolvedKey = (string) $resolvedKey;
}

yield $resolvedKey => $item;
}
});
}







public function has($key)
{
$keys = array_flip(is_array($key) ? $key : func_get_args());
$count = count($keys);

foreach ($this as $key => $value) {
if (array_key_exists($key, $keys) && --$count == 0) {
return true;
}
}

return false;
}







public function hasAny($key)
{
$keys = array_flip(is_array($key) ? $key : func_get_args());

foreach ($this as $key => $value) {
if (array_key_exists($key, $keys)) {
return true;
}
}

return false;
}








public function implode($value, $glue = null)
{
return $this->collect()->implode(...func_get_args());
}







public function intersect($items)
{
return $this->passthru('intersect', func_get_args());
}








public function intersectUsing($items, callable $callback)
{
return $this->passthru('intersectUsing', func_get_args());
}







public function intersectAssoc($items)
{
return $this->passthru('intersectAssoc', func_get_args());
}








public function intersectAssocUsing($items, callable $callback)
{
return $this->passthru('intersectAssocUsing', func_get_args());
}







public function intersectByKeys($items)
{
return $this->passthru('intersectByKeys', func_get_args());
}






public function isEmpty()
{
return ! $this->getIterator()->valid();
}






public function containsOneItem()
{
return $this->take(2)->count() === 1;
}








public function join($glue, $finalGlue = '')
{
return $this->collect()->join(...func_get_args());
}






public function keys()
{
return new static(function () {
foreach ($this as $key => $value) {
yield $key;
}
});
}

/**
@template






*/
public function last(?callable $callback = null, $default = null)
{
$needle = $placeholder = new stdClass;

foreach ($this as $key => $value) {
if (is_null($callback) || $callback($value, $key)) {
$needle = $value;
}
}

return $needle === $placeholder ? value($default) : $needle;
}








public function pluck($value, $key = null)
{
return new static(function () use ($value, $key) {
[$value, $key] = $this->explodePluckParameters($value, $key);

foreach ($this as $item) {
$itemValue = $value instanceof Closure
? $value($item)
: data_get($item, $value);

if (is_null($key)) {
yield $itemValue;
} else {
$itemKey = $key instanceof Closure
? $key($item)
: data_get($item, $key);

if (is_object($itemKey) && method_exists($itemKey, '__toString')) {
$itemKey = (string) $itemKey;
}

yield $itemKey => $itemValue;
}
}
});
}

/**
@template





*/
public function map(callable $callback)
{
return new static(function () use ($callback) {
foreach ($this as $key => $value) {
yield $key => $callback($value, $key);
}
});
}

/**
@template
@template







*/
public function mapToDictionary(callable $callback)
{
return $this->passthru('mapToDictionary', func_get_args());
}

/**
@template
@template







*/
public function mapWithKeys(callable $callback)
{
return new static(function () use ($callback) {
foreach ($this as $key => $value) {
yield from $callback($value, $key);
}
});
}







public function merge($items)
{
return $this->passthru('merge', func_get_args());
}

/**
@template





*/
public function mergeRecursive($items)
{
return $this->passthru('mergeRecursive', func_get_args());
}







public function multiply(int $multiplier)
{
return $this->passthru('multiply', func_get_args());
}

/**
@template





*/
public function combine($values)
{
return new static(function () use ($values) {
$values = $this->makeIterator($values);

$errorMessage = 'Both parameters should have an equal number of elements';

foreach ($this as $key) {
if (! $values->valid()) {
trigger_error($errorMessage, E_USER_WARNING);

break;
}

yield $key => $values->current();

$values->next();
}

if ($values->valid()) {
trigger_error($errorMessage, E_USER_WARNING);
}
});
}







public function union($items)
{
return $this->passthru('union', func_get_args());
}








public function nth($step, $offset = 0)
{
return new static(function () use ($step, $offset) {
$position = 0;

foreach ($this->slice($offset) as $item) {
if ($position % $step === 0) {
yield $item;
}

$position++;
}
});
}







public function only($keys)
{
if ($keys instanceof Enumerable) {
$keys = $keys->all();
} elseif (! is_null($keys)) {
$keys = is_array($keys) ? $keys : func_get_args();
}

return new static(function () use ($keys) {
if (is_null($keys)) {
yield from $this;
} else {
$keys = array_flip($keys);

foreach ($this as $key => $value) {
if (array_key_exists($key, $keys)) {
yield $key => $value;

unset($keys[$key]);

if (empty($keys)) {
break;
}
}
}
}
});
}







public function select($keys)
{
if ($keys instanceof Enumerable) {
$keys = $keys->all();
} elseif (! is_null($keys)) {
$keys = is_array($keys) ? $keys : func_get_args();
}

return new static(function () use ($keys) {
if (is_null($keys)) {
yield from $this;
} else {
foreach ($this as $item) {
$result = [];

foreach ($keys as $key) {
if (Arr::accessible($item) && Arr::exists($item, $key)) {
$result[$key] = $item[$key];
} elseif (is_object($item) && isset($item->{$key})) {
$result[$key] = $item->{$key};
}
}

yield $result;
}
}
});
}

/**
@template
@template





*/
public function concat($source)
{
return (new static(function () use ($source) {
yield from $this;
yield from $source;
}))->values();
}









public function random($number = null)
{
$result = $this->collect()->random(...func_get_args());

return is_null($number) ? $result : new static($result);
}







public function replace($items)
{
return new static(function () use ($items) {
$items = $this->getArrayableItems($items);

foreach ($this as $key => $value) {
if (array_key_exists($key, $items)) {
yield $key => $items[$key];

unset($items[$key]);
} else {
yield $key => $value;
}
}

foreach ($items as $key => $value) {
yield $key => $value;
}
});
}







public function replaceRecursive($items)
{
return $this->passthru('replaceRecursive', func_get_args());
}






public function reverse()
{
return $this->passthru('reverse', func_get_args());
}








public function search($value, $strict = false)
{

$predicate = $this->useAsCallable($value)
? $value
: function ($item) use ($value, $strict) {
return $strict ? $item === $value : $item == $value;
};

foreach ($this as $key => $item) {
if ($predicate($item, $key)) {
return $key;
}
}

return false;
}








public function before($value, $strict = false)
{
$previous = null;


$predicate = $this->useAsCallable($value)
? $value
: function ($item) use ($value, $strict) {
return $strict ? $item === $value : $item == $value;
};

foreach ($this as $key => $item) {
if ($predicate($item, $key)) {
return $previous;
}

$previous = $item;
}

return null;
}








public function after($value, $strict = false)
{
$found = false;


$predicate = $this->useAsCallable($value)
? $value
: function ($item) use ($value, $strict) {
return $strict ? $item === $value : $item == $value;
};

foreach ($this as $key => $item) {
if ($found) {
return $item;
}

if ($predicate($item, $key)) {
$found = true;
}
}

return null;
}






public function shuffle()
{
return $this->passthru('shuffle', []);
}








public function sliding($size = 2, $step = 1)
{
return new static(function () use ($size, $step) {
$iterator = $this->getIterator();

$chunk = [];

while ($iterator->valid()) {
$chunk[$iterator->key()] = $iterator->current();

if (count($chunk) == $size) {
yield (new static($chunk))->tap(function () use (&$chunk, $step) {
$chunk = array_slice($chunk, $step, null, true);
});




if ($step > $size) {
$skip = $step - $size;

for ($i = 0; $i < $skip && $iterator->valid(); $i++) {
$iterator->next();
}
}
}

$iterator->next();
}
});
}







public function skip($count)
{
return new static(function () use ($count) {
$iterator = $this->getIterator();

while ($iterator->valid() && $count--) {
$iterator->next();
}

while ($iterator->valid()) {
yield $iterator->key() => $iterator->current();

$iterator->next();
}
});
}







public function skipUntil($value)
{
$callback = $this->useAsCallable($value) ? $value : $this->equality($value);

return $this->skipWhile($this->negate($callback));
}







public function skipWhile($value)
{
$callback = $this->useAsCallable($value) ? $value : $this->equality($value);

return new static(function () use ($callback) {
$iterator = $this->getIterator();

while ($iterator->valid() && $callback($iterator->current(), $iterator->key())) {
$iterator->next();
}

while ($iterator->valid()) {
yield $iterator->key() => $iterator->current();

$iterator->next();
}
});
}








public function slice($offset, $length = null)
{
if ($offset < 0 || $length < 0) {
return $this->passthru('slice', func_get_args());
}

$instance = $this->skip($offset);

return is_null($length) ? $instance : $instance->take($length);
}







public function split($numberOfGroups)
{
return $this->passthru('split', func_get_args());
}












public function sole($key = null, $operator = null, $value = null)
{
$filter = func_num_args() > 1
? $this->operatorForWhere(...func_get_args())
: $key;

return $this
->unless($filter == null)
->filter($filter)
->take(2)
->collect()
->sole();
}











public function firstOrFail($key = null, $operator = null, $value = null)
{
$filter = func_num_args() > 1
? $this->operatorForWhere(...func_get_args())
: $key;

return $this
->unless($filter == null)
->filter($filter)
->take(1)
->collect()
->firstOrFail();
}








public function chunk($size, $preserveKeys = true)
{
if ($size <= 0) {
return static::empty();
}

$add = match ($preserveKeys) {
true => fn (array &$chunk, Traversable $iterator) => $chunk[$iterator->key()] = $iterator->current(),
false => fn (array &$chunk, Traversable $iterator) => $chunk[] = $iterator->current(),
};

return new static(function () use ($size, $add) {
$iterator = $this->getIterator();

while ($iterator->valid()) {
$chunk = [];

while (true) {
$add($chunk, $iterator);

if (count($chunk) < $size) {
$iterator->next();

if (! $iterator->valid()) {
break;
}
} else {
break;
}
}

yield new static($chunk);

$iterator->next();
}
});
}







public function splitIn($numberOfGroups)
{
return $this->chunk((int) ceil($this->count() / $numberOfGroups));
}







public function chunkWhile(callable $callback)
{
return new static(function () use ($callback) {
$iterator = $this->getIterator();

$chunk = new Collection;

if ($iterator->valid()) {
$chunk[$iterator->key()] = $iterator->current();

$iterator->next();
}

while ($iterator->valid()) {
if (! $callback($iterator->current(), $iterator->key(), $chunk)) {
yield new static($chunk);

$chunk = new Collection;
}

$chunk[$iterator->key()] = $iterator->current();

$iterator->next();
}

if ($chunk->isNotEmpty()) {
yield new static($chunk);
}
});
}







public function sort($callback = null)
{
return $this->passthru('sort', func_get_args());
}







public function sortDesc($options = SORT_REGULAR)
{
return $this->passthru('sortDesc', func_get_args());
}









public function sortBy($callback, $options = SORT_REGULAR, $descending = false)
{
return $this->passthru('sortBy', func_get_args());
}








public function sortByDesc($callback, $options = SORT_REGULAR)
{
return $this->passthru('sortByDesc', func_get_args());
}








public function sortKeys($options = SORT_REGULAR, $descending = false)
{
return $this->passthru('sortKeys', func_get_args());
}







public function sortKeysDesc($options = SORT_REGULAR)
{
return $this->passthru('sortKeysDesc', func_get_args());
}







public function sortKeysUsing(callable $callback)
{
return $this->passthru('sortKeysUsing', func_get_args());
}







public function take($limit)
{
if ($limit < 0) {
return new static(function () use ($limit) {
$limit = abs($limit);
$ringBuffer = [];
$position = 0;

foreach ($this as $key => $value) {
$ringBuffer[$position] = [$key, $value];
$position = ($position + 1) % $limit;
}

for ($i = 0, $end = min($limit, count($ringBuffer)); $i < $end; $i++) {
$pointer = ($position + $i) % $limit;
yield $ringBuffer[$pointer][0] => $ringBuffer[$pointer][1];
}
});
}

return new static(function () use ($limit) {
$iterator = $this->getIterator();

while ($limit--) {
if (! $iterator->valid()) {
break;
}

yield $iterator->key() => $iterator->current();

if ($limit) {
$iterator->next();
}
}
});
}







public function takeUntil($value)
{

$callback = $this->useAsCallable($value) ? $value : $this->equality($value);

return new static(function () use ($callback) {
foreach ($this as $key => $item) {
if ($callback($item, $key)) {
break;
}

yield $key => $item;
}
});
}







public function takeUntilTimeout(DateTimeInterface $timeout)
{
$timeout = $timeout->getTimestamp();

return new static(function () use ($timeout) {
if ($this->now() >= $timeout) {
return;
}

foreach ($this as $key => $value) {
yield $key => $value;

if ($this->now() >= $timeout) {
break;
}
}
});
}







public function takeWhile($value)
{

$callback = $this->useAsCallable($value) ? $value : $this->equality($value);

return $this->takeUntil(fn ($item, $key) => ! $callback($item, $key));
}







public function tapEach(callable $callback)
{
return new static(function () use ($callback) {
foreach ($this as $key => $value) {
$callback($value, $key);

yield $key => $value;
}
});
}






public function throttle(float $seconds)
{
return new static(function () use ($seconds) {
$microseconds = $seconds * 1_000_000;

foreach ($this as $key => $value) {
$fetchedAt = $this->preciseNow();

yield $key => $value;

$sleep = $microseconds - ($this->preciseNow() - $fetchedAt);

$this->usleep((int) $sleep);
}
});
}






public function dot()
{
return $this->passthru('dot', []);
}






public function undot()
{
return $this->passthru('undot', []);
}








public function unique($key = null, $strict = false)
{
$callback = $this->valueRetriever($key);

return new static(function () use ($callback, $strict) {
$exists = [];

foreach ($this as $key => $item) {
if (! in_array($id = $callback($item, $key), $exists, $strict)) {
yield $key => $item;

$exists[] = $id;
}
}
});
}






public function values()
{
return new static(function () {
foreach ($this as $item) {
yield $item;
}
});
}

/**
@template








*/
public function zip($items)
{
$iterables = func_get_args();

return new static(function () use ($iterables) {
$iterators = (new Collection($iterables))
->map(fn ($iterable) => $this->makeIterator($iterable))
->prepend($this->getIterator());

while ($iterators->contains->valid()) {
yield new static($iterators->map->current());

$iterators->each->next();
}
});
}

/**
@template






*/
public function pad($size, $value)
{
if ($size < 0) {
return $this->passthru('pad', func_get_args());
}

return new static(function () use ($size, $value) {
$yielded = 0;

foreach ($this as $index => $item) {
yield $index => $item;

$yielded++;
}

while ($yielded++ < $size) {
yield $value;
}
});
}






public function getIterator(): Traversable
{
return $this->makeIterator($this->source);
}






public function count(): int
{
if (is_array($this->source)) {
return count($this->source);
}

return iterator_count($this->getIterator());
}

/**
@template
@template





*/
protected function makeIterator($source)
{
if ($source instanceof IteratorAggregate) {
return $source->getIterator();
}

if (is_array($source)) {
return new ArrayIterator($source);
}

if (is_callable($source)) {
$maybeTraversable = $source();

return $maybeTraversable instanceof Traversable
? $maybeTraversable
: new ArrayIterator(Arr::wrap($maybeTraversable));
}

return new ArrayIterator((array) $source);
}








protected function explodePluckParameters($value, $key)
{
$value = is_string($value) ? explode('.', $value) : $value;

$key = is_null($key) || is_array($key) || $key instanceof Closure ? $key : explode('.', $key);

return [$value, $key];
}








protected function passthru($method, array $params)
{
return new static(function () use ($method, $params) {
yield from $this->collect()->$method(...$params);
});
}






protected function now()
{
return class_exists(Carbon::class)
? Carbon::now()->timestamp
: time();
}






protected function preciseNow()
{
return class_exists(Carbon::class)
? Carbon::now()->getPreciseTimestamp()
: microtime(true) * 1_000_000;
}






protected function usleep(int $microseconds)
{
if ($microseconds <= 0) {
return;
}

class_exists(Sleep::class)
? Sleep::usleep($microseconds)
: usleep($microseconds);
}
}
