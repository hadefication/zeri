<?php

declare(strict_types=1);

namespace PHPUnit\Architecture\Elements\Layer;

use ArrayIterator;
use Closure;
use IteratorAggregate;
use PHPUnit\Architecture\Elements\ObjectDescription;
use Traversable;

/**
@implements
*/
final class Layer implements IteratorAggregate
{
use LayerLeave;
use LayerExclude;
use LayerSplit;

protected ?string $name = null;




protected array $objects = [];




public function __construct(
array $objects
) {
$this->objects = $objects;
}

#[\ReturnTypeWillChange]
public function getIterator(): Traversable
{
return new ArrayIterator($this->objects);
}

public function getName(): string
{
if ($this->name === null) {
$objectsName = array_map(static function (ObjectDescription $objectDescription): string {
return $objectDescription->name;
}, $this->objects);

sort($objectsName);

$this->name = implode(',', $objectsName);
}

return $this->name;
}




public function equals(Layer $layer): bool
{
return $this->getName() === $layer->getName();
}




public function leave(Closure $closure): self
{
return new Layer(array_filter($this->objects, $closure));
}




public function exclude(Closure $closure): self
{
return new Layer(array_filter($this->objects, static function ($item) use ($closure): bool {
return !$closure($item);
}));
}





public function split(Closure $closure): array
{
$objects = [];

foreach ($this->objects as $object) {

$key = $closure($object);

if ($key === null) {
continue;
}

if (!isset($objects[$key])) {
$objects[$key] = [];
}

$objects[$key][] = $object;
}

return array_map(static function (array $objects): Layer {
return new Layer($objects);
}, $objects);
}




public function essence(string $path): array
{
return $this->essenceRecursion(
'',
explode('.', $path),
$this->objects
);
}







private function essenceRecursion(string $path, array $parts, $list): array
{
$part = array_shift($parts);
if ($part === null) {
return $list;
}

$result = [];

if ($part === '*') {
foreach ($list as $key => $item) {

$result = array_merge($result, $this->essenceRecursion("$path.$key", $parts, $item));
}

return $result;
}

foreach ($list as $key => $item) {
$result["$path.$key"] = $item->$part;
}

return $this->essenceRecursion($path, $parts, $result);
}
}
