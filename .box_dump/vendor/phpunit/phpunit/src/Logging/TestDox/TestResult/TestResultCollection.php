<?php declare(strict_types=1);








namespace PHPUnit\Logging\TestDox;

use IteratorAggregate;

/**
@template-implements
@immutable
@no-named-arguments




*/
final readonly class TestResultCollection implements IteratorAggregate
{



private array $testResults;




public static function fromArray(array $testResults): self
{
return new self(...$testResults);
}

private function __construct(TestResult ...$testResults)
{
$this->testResults = $testResults;
}




public function asArray(): array
{
return $this->testResults;
}

public function getIterator(): TestResultCollectionIterator
{
return new TestResultCollectionIterator($this);
}
}
