<?php declare(strict_types=1);








namespace PHPUnit\Event;

/**
@no-named-arguments


*/
final class CollectingDispatcher implements Dispatcher
{
private EventCollection $events;

public function __construct()
{
$this->events = new EventCollection;
}

public function dispatch(Event $event): void
{
$this->events->add($event);
}

public function flush(): EventCollection
{
$events = $this->events;

$this->events = new EventCollection;

return $events;
}
}
