<?php

namespace Illuminate\Support;

use CachingIterator;
use Countable;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use IteratorAggregate;
use JsonSerializable;
use Traversable;

/**
@template
@template-covariant
@extends
@extends


*/
interface Enumerable extends Arrayable, Countable, IteratorAggregate, Jsonable, JsonSerializable
{
/**
@template
@template





*/
public static function make($items = []);








public static function times($number, ?callable $callback = null);









public static function range($from, $to, $step = 1);

/**
@template





*/
public static function wrap($value);

/**
@template
@template





*/
public static function unwrap($value);






public static function empty();






public function all();







public function average($callback = null);







public function median($key = null);







public function mode($key = null);






public function collapse();









public function some($key, $operator = null, $value = null);








public function containsStrict($key, $value = null);







public function avg($callback = null);









public function contains($key, $operator = null, $value = null);









public function doesntContain($key, $operator = null, $value = null);

/**
@template
@template





*/
public function crossJoin(...$lists);







public function dd(...$args);







public function dump(...$args);







public function diff($items);








public function diffUsing($items, callable $callback);







public function diffAssoc($items);








public function diffAssocUsing($items, callable $callback);







public function diffKeys($items);








public function diffKeysUsing($items, callable $callback);








public function duplicates($callback = null, $strict = false);







public function duplicatesStrict($callback = null);







public function each(callable $callback);







public function eachSpread(callable $callback);









public function every($key, $operator = null, $value = null);







public function except($keys);







public function filter(?callable $callback = null);

/**
@template







*/
public function when($value, ?callable $callback = null, ?callable $default = null);

/**
@template






*/
public function whenEmpty(callable $callback, ?callable $default = null);

/**
@template






*/
public function whenNotEmpty(callable $callback, ?callable $default = null);

/**
@template







*/
public function unless($value, callable $callback, ?callable $default = null);

/**
@template






*/
public function unlessEmpty(callable $callback, ?callable $default = null);

/**
@template






*/
public function unlessNotEmpty(callable $callback, ?callable $default = null);









public function where($key, $operator = null, $value = null);







public function whereNull($key = null);







public function whereNotNull($key = null);








public function whereStrict($key, $value);









public function whereIn($key, $values, $strict = false);








public function whereInStrict($key, $values);








public function whereBetween($key, $values);








public function whereNotBetween($key, $values);









public function whereNotIn($key, $values, $strict = false);








public function whereNotInStrict($key, $values);

/**
@template





*/
public function whereInstanceOf($type);

/**
@template






*/
public function first(?callable $callback = null, $default = null);









public function firstWhere($key, $operator = null, $value = null);







public function flatten($depth = INF);






public function flip();

/**
@template






*/
public function get($key, $default = null);

/**
@template






*/
public function groupBy($groupBy, $preserveKeys = false);

/**
@template





*/
public function keyBy($keyBy);







public function has($key);







public function hasAny($key);








public function implode($value, $glue = null);







public function intersect($items);








public function intersectUsing($items, callable $callback);







public function intersectAssoc($items);








public function intersectAssocUsing($items, callable $callback);







public function intersectByKeys($items);






public function isEmpty();






public function isNotEmpty();






public function containsOneItem();








public function join($glue, $finalGlue = '');






public function keys();

/**
@template






*/
public function last(?callable $callback = null, $default = null);

/**
@template





*/
public function map(callable $callback);







public function mapSpread(callable $callback);

/**
@template
@template







*/
public function mapToDictionary(callable $callback);

/**
@template
@template







*/
public function mapToGroups(callable $callback);

/**
@template
@template







*/
public function mapWithKeys(callable $callback);

/**
@template
@template





*/
public function flatMap(callable $callback);

/**
@template





*/
public function mapInto($class);







public function merge($items);

/**
@template





*/
public function mergeRecursive($items);

/**
@template





*/
public function combine($values);







public function union($items);







public function min($callback = null);







public function max($callback = null);








public function nth($step, $offset = 0);







public function only($keys);








public function forPage($page, $perPage);









public function partition($key, $operator = null, $value = null);

/**
@template
@template





*/
public function concat($source);









public function random($number = null);

/**
@template
@template






*/
public function reduce(callable $callback, $initial = null);










public function reduceSpread(callable $callback, ...$initial);







public function replace($items);







public function replaceRecursive($items);






public function reverse();








public function search($value, $strict = false);








public function before($value, $strict = false);








public function after($value, $strict = false);






public function shuffle();








public function sliding($size = 2, $step = 1);







public function skip($count);







public function skipUntil($value);







public function skipWhile($value);








public function slice($offset, $length = null);







public function split($numberOfGroups);












public function sole($key = null, $operator = null, $value = null);











public function firstOrFail($key = null, $operator = null, $value = null);







public function chunk($size);







public function chunkWhile(callable $callback);







public function splitIn($numberOfGroups);







public function sort($callback = null);







public function sortDesc($options = SORT_REGULAR);









public function sortBy($callback, $options = SORT_REGULAR, $descending = false);








public function sortByDesc($callback, $options = SORT_REGULAR);








public function sortKeys($options = SORT_REGULAR, $descending = false);







public function sortKeysDesc($options = SORT_REGULAR);







public function sortKeysUsing(callable $callback);







public function sum($callback = null);







public function take($limit);







public function takeUntil($value);







public function takeWhile($value);







public function tap(callable $callback);

/**
@template





*/
public function pipe(callable $callback);

/**
@template





*/
public function pipeInto($class);







public function pipeThrough($pipes);








public function pluck($value, $key = null);







public function reject($callback = true);






public function undot();








public function unique($key = null, $strict = false);







public function uniqueStrict($key = null);






public function values();

/**
@template






*/
public function pad($size, $value);






public function getIterator(): Traversable;






public function count(): int;







public function countBy($countBy = null);

/**
@template








*/
public function zip($items);






public function collect();






public function toArray();






public function jsonSerialize(): mixed;







public function toJson($options = 0);







public function getCachingIterator($flags = CachingIterator::CALL_TOSTRING);






public function __toString();







public function escapeWhenCastingToString($escape = true);







public static function proxy($method);









public function __get($key);
}
