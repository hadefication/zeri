<?php declare(strict_types=1);








namespace PHPUnit\Event\Code;

use function count;
use Iterator;

/**
@template-implements
@no-named-arguments

*/
final class TestCollectionIterator implements Iterator
{



private readonly array $tests;
private int $position = 0;

public function __construct(TestCollection $tests)
{
$this->tests = $tests->asArray();
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
}
