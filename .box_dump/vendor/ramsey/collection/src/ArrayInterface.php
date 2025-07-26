<?php











declare(strict_types=1);

namespace Ramsey\Collection;

use ArrayAccess;
use Countable;
use IteratorAggregate;

/**
@template
@extends
@extends


*/
interface ArrayInterface extends
ArrayAccess,
Countable,
IteratorAggregate
{



public function clear(): void;






public function toArray(): array;




public function isEmpty(): bool;
}
