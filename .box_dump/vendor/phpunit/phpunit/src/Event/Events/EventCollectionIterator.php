<?php declare(strict_types=1);








namespace PHPUnit\Event;

use function count;
use Iterator;

/**
@template-implements
@no-named-arguments

*/
final class EventCollectionIterator implements Iterator
{



private readonly array $events;
private int $position = 0;

public function __construct(EventCollection $events)
{
$this->events = $events->asArray();
}

public function rewind(): void
{
$this->position = 0;
}

public function valid(): bool
{
return $this->position < count($this->events);
}

public function key(): int
{
return $this->position;
}

public function current(): Event
{
return $this->events[$this->position];
}

public function next(): void
{
$this->position++;
}
}
