<?php

declare(strict_types=1);

namespace PHPUnit\Architecture\Asserts\Methods\Elements;

use ArrayIterator;
use IteratorAggregate;
use Traversable;

/**
@implements
*/
final class ObjectMethods implements IteratorAggregate
{





protected array $methods;




public function __construct(array $methods)
{
$this->methods = $methods;
}

#[\ReturnTypeWillChange]
public function getIterator(): Traversable
{
return new ArrayIterator($this->methods);
}
}
