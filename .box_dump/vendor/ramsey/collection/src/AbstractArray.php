<?php











declare(strict_types=1);

namespace Ramsey\Collection;

use ArrayIterator;
use Traversable;

use function count;

/**
@template
@implements



*/
abstract class AbstractArray implements ArrayInterface
{





protected array $data = [];






public function __construct(array $data = [])
{


foreach ($data as $key => $value) {
$this[$key] = $value;
}
}








public function getIterator(): Traversable
{
return new ArrayIterator($this->data);
}








public function offsetExists(mixed $offset): bool
{
return isset($this->data[$offset]);
}











public function offsetGet(mixed $offset): mixed
{
return $this->data[$offset];
}










public function offsetSet(mixed $offset, mixed $value): void
{
if ($offset === null) {
$this->data[] = $value;
} else {
$this->data[$offset] = $value;
}
}








public function offsetUnset(mixed $offset): void
{
unset($this->data[$offset]);
}









public function __serialize(): array
{
return $this->data;
}






public function __unserialize(array $data): void
{
$this->data = $data;
}






public function count(): int
{
return count($this->data);
}

public function clear(): void
{
$this->data = [];
}




public function toArray(): array
{
return $this->data;
}

public function isEmpty(): bool
{
return $this->data === [];
}
}
