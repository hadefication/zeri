<?php declare(strict_types=1);








namespace PHPUnit\Event\Code;

use function count;
use Countable;
use IteratorAggregate;

/**
@template-implements
@immutable
@no-named-arguments


*/
final readonly class TestCollection implements Countable, IteratorAggregate
{



private array $tests;




public static function fromArray(array $tests): self
{
return new self(...$tests);
}

private function __construct(Test ...$tests)
{
$this->tests = $tests;
}




public function asArray(): array
{
return $this->tests;
}

public function count(): int
{
return count($this->tests);
}

public function getIterator(): TestCollectionIterator
{
return new TestCollectionIterator($this);
}
}
