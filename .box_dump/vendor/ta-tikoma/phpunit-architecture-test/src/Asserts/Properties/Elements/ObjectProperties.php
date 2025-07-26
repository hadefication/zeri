<?php

declare(strict_types=1);

namespace PHPUnit\Architecture\Asserts\Properties\Elements;

use ArrayIterator;
use IteratorAggregate;
use Traversable;

/**
@implements
*/
final class ObjectProperties implements IteratorAggregate
{





public array $properties;




public function __construct(array $properties)
{
$this->properties = $properties;
}

#[\ReturnTypeWillChange]
public function getIterator(): Traversable
{
return new ArrayIterator($this->properties);
}
}
