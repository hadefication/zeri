<?php declare(strict_types=1);








namespace PHPUnit\TextUI\Configuration;

use function count;
use Iterator;

/**
@no-named-arguments
@template-implements

*/
final class TestDirectoryCollectionIterator implements Iterator
{



private readonly array $directories;
private int $position = 0;

public function __construct(TestDirectoryCollection $directories)
{
$this->directories = $directories->asArray();
}

public function rewind(): void
{
$this->position = 0;
}

public function valid(): bool
{
return $this->position < count($this->directories);
}

public function key(): int
{
return $this->position;
}

public function current(): TestDirectory
{
return $this->directories[$this->position];
}

public function next(): void
{
$this->position++;
}
}
