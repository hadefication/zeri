<?php declare(strict_types=1);








namespace PHPUnit\TextUI\Configuration;

use function count;
use Countable;
use IteratorAggregate;

/**
@no-named-arguments
@immutable
@template-implements


*/
final readonly class VariableCollection implements Countable, IteratorAggregate
{



private array $variables;




public static function fromArray(array $variables): self
{
return new self(...$variables);
}

private function __construct(Variable ...$variables)
{
$this->variables = $variables;
}




public function asArray(): array
{
return $this->variables;
}

public function count(): int
{
return count($this->variables);
}

public function getIterator(): VariableCollectionIterator
{
return new VariableCollectionIterator($this);
}
}
