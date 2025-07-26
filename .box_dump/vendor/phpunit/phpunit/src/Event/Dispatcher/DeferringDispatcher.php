<?php declare(strict_types=1);








namespace PHPUnit\Event;

/**
@no-named-arguments


*/
final class DeferringDispatcher implements SubscribableDispatcher
{
private readonly SubscribableDispatcher $dispatcher;
private EventCollection $events;
private bool $recording = true;

public function __construct(SubscribableDispatcher $dispatcher)
{
$this->dispatcher = $dispatcher;
$this->events = new EventCollection;
}

public function registerTracer(Tracer\Tracer $tracer): void
{
$this->dispatcher->registerTracer($tracer);
}

public function registerSubscriber(Subscriber $subscriber): void
{
$this->dispatcher->registerSubscriber($subscriber);
}

public function dispatch(Event $event): void
{
if ($this->recording) {
$this->events->add($event);

return;
}

$this->dispatcher->dispatch($event);
}

public function flush(): void
{
$this->recording = false;

foreach ($this->events as $event) {
$this->dispatcher->dispatch($event);
}

$this->events = new EventCollection;
}
}
