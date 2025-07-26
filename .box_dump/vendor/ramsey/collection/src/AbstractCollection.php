<?php











declare(strict_types=1);

namespace Ramsey\Collection;

use Closure;
use Ramsey\Collection\Exception\CollectionMismatchException;
use Ramsey\Collection\Exception\InvalidArgumentException;
use Ramsey\Collection\Exception\InvalidPropertyOrMethod;
use Ramsey\Collection\Exception\NoSuchElementException;
use Ramsey\Collection\Exception\UnsupportedOperationException;
use Ramsey\Collection\Tool\TypeTrait;
use Ramsey\Collection\Tool\ValueExtractorTrait;
use Ramsey\Collection\Tool\ValueToStringTrait;

use function array_filter;
use function array_key_first;
use function array_key_last;
use function array_map;
use function array_merge;
use function array_reduce;
use function array_search;
use function array_udiff;
use function array_uintersect;
use function in_array;
use function is_int;
use function is_object;
use function spl_object_id;
use function sprintf;
use function usort;

/**
@template
@extends
@implements



*/
abstract class AbstractCollection extends AbstractArray implements CollectionInterface
{
use TypeTrait;
use ValueToStringTrait;
use ValueExtractorTrait;




public function add(mixed $element): bool
{
$this[] = $element;

return true;
}

public function contains(mixed $element, bool $strict = true): bool
{
return in_array($element, $this->data, $strict);
}




public function offsetSet(mixed $offset, mixed $value): void
{
if ($this->checkType($this->getType(), $value) === false) {
throw new InvalidArgumentException(
'Value must be of type ' . $this->getType() . '; value is '
. $this->toolValueToString($value),
);
}

if ($offset === null) {
$this->data[] = $value;
} else {
$this->data[$offset] = $value;
}
}

public function remove(mixed $element): bool
{
if (($position = array_search($element, $this->data, true)) !== false) {
unset($this[$position]);

return true;
}

return false;
}









public function column(string $propertyOrMethod): array
{
$temp = [];

foreach ($this->data as $item) {
$temp[] = $this->extractValue($item, $propertyOrMethod);
}

return $temp;
}






public function first(): mixed
{
$firstIndex = array_key_first($this->data);

if ($firstIndex === null) {
throw new NoSuchElementException('Can\'t determine first item. Collection is empty');
}

return $this->data[$firstIndex];
}






public function last(): mixed
{
$lastIndex = array_key_last($this->data);

if ($lastIndex === null) {
throw new NoSuchElementException('Can\'t determine last item. Collection is empty');
}

return $this->data[$lastIndex];
}









public function sort(?string $propertyOrMethod = null, Sort $order = Sort::Ascending): CollectionInterface
{
$collection = clone $this;

usort(
$collection->data,
function (mixed $a, mixed $b) use ($propertyOrMethod, $order): int {
$aValue = $this->extractValue($a, $propertyOrMethod);
$bValue = $this->extractValue($b, $propertyOrMethod);

return ($aValue <=> $bValue) * ($order === Sort::Descending ? -1 : 1);
},
);

return $collection;
}






public function filter(callable $callback): CollectionInterface
{
$collection = clone $this;
$collection->data = array_merge([], array_filter($collection->data, $callback));

return $collection;
}









public function where(?string $propertyOrMethod, mixed $value): CollectionInterface
{
return $this->filter(
fn (mixed $item): bool => $this->extractValue($item, $propertyOrMethod) === $value,
);
}

/**
@template





*/
public function map(callable $callback): CollectionInterface
{
return new Collection('mixed', array_map($callback, $this->data));
}

/**
@template






*/
public function reduce(callable $callback, mixed $initial): mixed
{
return array_reduce($this->data, $callback, $initial);
}










public function diff(CollectionInterface $other): CollectionInterface
{
$this->compareCollectionTypes($other);

$diffAtoB = array_udiff($this->data, $other->toArray(), $this->getComparator());
$diffBtoA = array_udiff($other->toArray(), $this->data, $this->getComparator());

$collection = clone $this;
$collection->data = array_merge($diffAtoB, $diffBtoA);

return $collection;
}










public function intersect(CollectionInterface $other): CollectionInterface
{
$this->compareCollectionTypes($other);

$collection = clone $this;
$collection->data = array_uintersect($this->data, $other->toArray(), $this->getComparator());

return $collection;
}










public function merge(CollectionInterface ...$collections): CollectionInterface
{
$mergedCollection = clone $this;

foreach ($collections as $index => $collection) {
if (!$collection instanceof static) {
throw new CollectionMismatchException(
sprintf('Collection with index %d must be of type %s', $index, static::class),
);
}



if ($this->getUniformType($collection) !== $this->getUniformType($this)) {
throw new CollectionMismatchException(
sprintf(
'Collection items in collection with index %d must be of type %s',
$index,
$this->getType(),
),
);
}

foreach ($collection as $key => $value) {
if (is_int($key)) {
$mergedCollection[] = $value;
} else {
$mergedCollection[$key] = $value;
}
}
}

return $mergedCollection;
}






private function compareCollectionTypes(CollectionInterface $other): void
{
if (!$other instanceof static) {
throw new CollectionMismatchException('Collection must be of type ' . static::class);
}



if ($this->getUniformType($other) !== $this->getUniformType($this)) {
throw new CollectionMismatchException('Collection items must be of type ' . $this->getType());
}
}

private function getComparator(): Closure
{
return function (mixed $a, mixed $b): int {





if (is_object($a) && is_object($b)) {
$a = spl_object_id($a);
$b = spl_object_id($b);
}

return $a === $b ? 0 : ($a < $b ? 1 : -1);
};
}




private function getUniformType(CollectionInterface $collection): string
{
return match ($collection->getType()) {
'integer' => 'int',
'boolean' => 'bool',
'double' => 'float',
default => $collection->getType(),
};
}
}
