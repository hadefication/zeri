<?php declare(strict_types=1);








namespace PHPUnit\Event;

use function array_key_exists;
use function class_exists;
use function class_implements;
use function in_array;
use function interface_exists;
use function sprintf;

/**
@no-named-arguments


*/
final class TypeMap
{



private array $mapping = [];












public function addMapping(string $subscriberInterface, string $eventClass): void
{
$this->ensureSubscriberInterfaceExists($subscriberInterface);
$this->ensureSubscriberInterfaceExtendsInterface($subscriberInterface);
$this->ensureEventClassExists($eventClass);
$this->ensureEventClassImplementsEventInterface($eventClass);
$this->ensureSubscriberWasNotAlreadyRegistered($subscriberInterface);
$this->ensureEventWasNotAlreadyAssigned($eventClass);

$this->mapping[$subscriberInterface] = $eventClass;
}

public function isKnownSubscriberType(Subscriber $subscriber): bool
{
foreach (class_implements($subscriber) as $interface) {
if (array_key_exists($interface, $this->mapping)) {
return true;
}
}

return false;
}

public function isKnownEventType(Event $event): bool
{
return in_array($event::class, $this->mapping, true);
}






public function map(Subscriber $subscriber): string
{
foreach (class_implements($subscriber) as $interface) {
if (array_key_exists($interface, $this->mapping)) {
return $this->mapping[$interface];
}
}

throw new MapError(
sprintf(
'Subscriber "%s" does not implement a known interface',
$subscriber::class,
),
);
}






private function ensureSubscriberInterfaceExists(string $subscriberInterface): void
{
if (!interface_exists($subscriberInterface)) {
throw new UnknownSubscriberException(
sprintf(
'Subscriber "%s" does not exist or is not an interface',
$subscriberInterface,
),
);
}
}






private function ensureEventClassExists(string $eventClass): void
{
if (!class_exists($eventClass)) {
throw new UnknownEventException(
sprintf(
'Event class "%s" does not exist',
$eventClass,
),
);
}
}






private function ensureSubscriberInterfaceExtendsInterface(string $subscriberInterface): void
{
if (!in_array(Subscriber::class, class_implements($subscriberInterface), true)) {
throw new InvalidSubscriberException(
sprintf(
'Subscriber "%s" does not extend Subscriber interface',
$subscriberInterface,
),
);
}
}






private function ensureEventClassImplementsEventInterface(string $eventClass): void
{
if (!in_array(Event::class, class_implements($eventClass), true)) {
throw new InvalidEventException(
sprintf(
'Event "%s" does not implement Event interface',
$eventClass,
),
);
}
}






private function ensureSubscriberWasNotAlreadyRegistered(string $subscriberInterface): void
{
if (array_key_exists($subscriberInterface, $this->mapping)) {
throw new SubscriberTypeAlreadyRegisteredException(
sprintf(
'Subscriber type "%s" already registered',
$subscriberInterface,
),
);
}
}






private function ensureEventWasNotAlreadyAssigned(string $eventClass): void
{
if (in_array($eventClass, $this->mapping, true)) {
throw new EventAlreadyAssignedException(
sprintf(
'Event "%s" already assigned',
$eventClass,
),
);
}
}
}
