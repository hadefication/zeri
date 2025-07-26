<?php declare(strict_types=1);








namespace PHPUnit\TextUI\Configuration;

use function count;
use Iterator;

/**
@no-named-arguments
@template-implements

*/
final class VariableCollectionIterator implements Iterator
{



private readonly array $variables;
private int $position = 0;

public function __construct(VariableCollection $variables)
{
$this->variables = $variables->asArray();
}

public function rewind(): void
{
$this->position = 0;
}

public function valid(): bool
{
return $this->position < count($this->variables);
}

public function key(): int
{
return $this->position;
}

public function current(): Variable
{
return $this->variables[$this->position];
}

public function next(): void
{
$this->position++;
}
}
