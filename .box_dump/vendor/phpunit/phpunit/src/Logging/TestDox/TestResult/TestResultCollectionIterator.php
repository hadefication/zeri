<?php declare(strict_types=1);








namespace PHPUnit\Logging\TestDox;

use function count;
use Iterator;

/**
@template-implements
@no-named-arguments



*/
final class TestResultCollectionIterator implements Iterator
{



private readonly array $testResults;
private int $position = 0;

public function __construct(TestResultCollection $testResults)
{
$this->testResults = $testResults->asArray();
}

public function rewind(): void
{
$this->position = 0;
}

public function valid(): bool
{
return $this->position < count($this->testResults);
}

public function key(): int
{
return $this->position;
}

public function current(): TestResult
{
return $this->testResults[$this->position];
}

public function next(): void
{
$this->position++;
}
}
