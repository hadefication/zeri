<?php

declare(strict_types=1);

namespace PHPUnit\Architecture\Asserts\Dependencies\Elements;

use ArrayIterator;
use IteratorAggregate;
use Traversable;

/**
@implements
*/
final class ObjectUses implements IteratorAggregate
{





protected array $uses;




public function __construct(array $uses)
{
$this->uses = $uses;
}

#[\ReturnTypeWillChange]
public function getIterator(): Traversable
{
return new ArrayIterator($this->uses);
}

public function getByName(string $name): ?string
{
$length = strlen($name);
foreach ($this as $use) {
if (substr($use, -$length, $length) === $name) {
return $use;
}
}

return null;
}
}
