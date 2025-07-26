<?php declare(strict_types=1);








namespace PHPUnit\Event\TestData;

use function count;
use Iterator;

/**
@template-implements
@no-named-arguments

*/
final class TestDataCollectionIterator implements Iterator
{



private readonly array $data;
private int $position = 0;

public function __construct(TestDataCollection $data)
{
$this->data = $data->asArray();
}

public function rewind(): void
{
$this->position = 0;
}

public function valid(): bool
{
return $this->position < count($this->data);
}

public function key(): int
{
return $this->position;
}

public function current(): TestData
{
return $this->data[$this->position];
}

public function next(): void
{
$this->position++;
}
}
