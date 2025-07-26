<?php declare(strict_types=1);








namespace PHPUnit\Framework;

use function assert;
use function count;
use RecursiveIterator;

/**
@template-implements
@no-named-arguments



*/
final class TestSuiteIterator implements RecursiveIterator
{
private int $position = 0;




private readonly array $tests;

public function __construct(TestSuite $testSuite)
{
$this->tests = $testSuite->tests();
}

public function rewind(): void
{
$this->position = 0;
}

public function valid(): bool
{
return $this->position < count($this->tests);
}

public function key(): int
{
return $this->position;
}

public function current(): Test
{
return $this->tests[$this->position];
}

public function next(): void
{
$this->position++;
}




public function getChildren(): self
{
if (!$this->hasChildren()) {
throw new NoChildTestSuiteException(
'The current item is not a TestSuite instance and therefore does not have any children.',
);
}

$current = $this->current();

assert($current instanceof TestSuite);

return new self($current);
}

public function hasChildren(): bool
{
return $this->valid() && $this->current() instanceof TestSuite;
}
}
