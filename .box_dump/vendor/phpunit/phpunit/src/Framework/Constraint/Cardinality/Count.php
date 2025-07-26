<?php declare(strict_types=1);








namespace PHPUnit\Framework\Constraint;

use function count;
use function is_countable;
use function iterator_count;
use function sprintf;
use EmptyIterator;
use Generator;
use Iterator;
use IteratorAggregate;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\GeneratorNotSupportedException;
use Traversable;

/**
@no-named-arguments
*/
class Count extends Constraint
{
private readonly int $expectedCount;

public function __construct(int $expected)
{
$this->expectedCount = $expected;
}

public function toString(): string
{
return sprintf(
'count matches %d',
$this->expectedCount,
);
}







protected function matches(mixed $other): bool
{
return $this->expectedCount === $this->getCountOf($other);
}




protected function getCountOf(mixed $other): ?int
{
if (is_countable($other)) {
return count($other);
}

if ($other instanceof EmptyIterator) {
return 0;
}

if ($other instanceof Traversable) {
while ($other instanceof IteratorAggregate) {
try {
$other = $other->getIterator();
} catch (\Exception $e) {
throw new Exception(
$e->getMessage(),
$e->getCode(),
$e,
);
}
}

$iterator = $other;

if ($iterator instanceof Generator) {
throw new GeneratorNotSupportedException;
}

if (!$iterator instanceof Iterator) {
return iterator_count($iterator);
}

$key = $iterator->key();
$count = iterator_count($iterator);



if ($key !== null) {
$iterator->rewind();

while ($iterator->valid() && $key !== $iterator->key()) {
$iterator->next();
}
}

return $count;
}

return null;
}









protected function failureDescription(mixed $other): string
{
return sprintf(
'actual size %d matches expected size %d',
(int) $this->getCountOf($other),
$this->expectedCount,
);
}
}
