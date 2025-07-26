<?php declare(strict_types=1);








namespace PHPUnit\TextUI\Configuration;

use function count;
use Iterator;

/**
@no-named-arguments
@template-implements

*/
final class GroupCollectionIterator implements Iterator
{



private readonly array $groups;
private int $position = 0;

public function __construct(GroupCollection $groups)
{
$this->groups = $groups->asArray();
}

public function rewind(): void
{
$this->position = 0;
}

public function valid(): bool
{
return $this->position < count($this->groups);
}

public function key(): int
{
return $this->position;
}

public function current(): Group
{
return $this->groups[$this->position];
}

public function next(): void
{
$this->position++;
}
}
