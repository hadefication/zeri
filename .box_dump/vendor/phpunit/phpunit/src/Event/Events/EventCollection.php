<?php declare(strict_types=1);








namespace PHPUnit\Event;

use function count;
use Countable;
use IteratorAggregate;

/**
@template-implements
@no-named-arguments

*/
final class EventCollection implements Countable, IteratorAggregate
{



private array $events = [];

public function add(Event ...$events): void
{
foreach ($events as $event) {
$this->events[] = $event;
}
}




public function asArray(): array
{
return $this->events;
}

public function count(): int
{
return count($this->events);
}

public function isEmpty(): bool
{
return $this->count() === 0;
}

public function isNotEmpty(): bool
{
return $this->count() > 0;
}

public function getIterator(): EventCollectionIterator
{
return new EventCollectionIterator($this);
}
}
