<?php











declare(strict_types=1);

namespace Ramsey\Collection;

use Ramsey\Collection\Exception\CollectionMismatchException;
use Ramsey\Collection\Exception\InvalidArgumentException;
use Ramsey\Collection\Exception\InvalidPropertyOrMethod;
use Ramsey\Collection\Exception\NoSuchElementException;
use Ramsey\Collection\Exception\UnsupportedOperationException;

/**
@template
@extends





*/
interface CollectionInterface extends ArrayInterface
{



























public function add(mixed $element): bool;







public function contains(mixed $element, bool $strict = true): bool;




public function getType(): string;









public function remove(mixed $element): bool;














public function column(string $propertyOrMethod): array;








public function first(): mixed;








public function last(): mixed;





















public function sort(?string $propertyOrMethod = null, Sort $order = Sort::Ascending): self;















public function filter(callable $callback): self;



















public function where(?string $propertyOrMethod, mixed $value): self;

/**
@template














*/
public function map(callable $callback): self;

/**
@template












*/
public function reduce(callable $callback, mixed $initial): mixed;













public function diff(CollectionInterface $other): self;













public function intersect(CollectionInterface $other): self;












public function merge(CollectionInterface ...$collections): self;
}
