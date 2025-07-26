<?php










namespace Symfony\Component\EventDispatcher;

use Psr\EventDispatcher\StoppableEventInterface;
use Symfony\Component\EventDispatcher\Debug\WrappedListener;
















class EventDispatcher implements EventDispatcherInterface
{
private array $listeners = [];
private array $sorted = [];
private array $optimized;

public function __construct()
{
if (__CLASS__ === static::class) {
$this->optimized = [];
}
}

public function dispatch(object $event, ?string $eventName = null): object
{
$eventName ??= $event::class;

if (isset($this->optimized)) {
$listeners = $this->optimized[$eventName] ?? (empty($this->listeners[$eventName]) ? [] : $this->optimizeListeners($eventName));
} else {
$listeners = $this->getListeners($eventName);
}

if ($listeners) {
$this->callListeners($listeners, $eventName, $event);
}

return $event;
}

public function getListeners(?string $eventName = null): array
{
if (null !== $eventName) {
if (empty($this->listeners[$eventName])) {
return [];
}

if (!isset($this->sorted[$eventName])) {
$this->sortListeners($eventName);
}

return $this->sorted[$eventName];
}

foreach ($this->listeners as $eventName => $eventListeners) {
if (!isset($this->sorted[$eventName])) {
$this->sortListeners($eventName);
}
}

return array_filter($this->sorted);
}

public function getListenerPriority(string $eventName, callable|array $listener): ?int
{
if (empty($this->listeners[$eventName])) {
return null;
}

if (\is_array($listener) && isset($listener[0]) && $listener[0] instanceof \Closure && 2 >= \count($listener)) {
$listener[0] = $listener[0]();
$listener[1] ??= '__invoke';
}

foreach ($this->listeners[$eventName] as $priority => &$listeners) {
foreach ($listeners as &$v) {
if ($v !== $listener && \is_array($v) && isset($v[0]) && $v[0] instanceof \Closure && 2 >= \count($v)) {
$v[0] = $v[0]();
$v[1] ??= '__invoke';
}
if ($v === $listener || ($listener instanceof \Closure && $v == $listener)) {
return $priority;
}
}
}

return null;
}

public function hasListeners(?string $eventName = null): bool
{
if (null !== $eventName) {
return !empty($this->listeners[$eventName]);
}

foreach ($this->listeners as $eventListeners) {
if ($eventListeners) {
return true;
}
}

return false;
}

public function addListener(string $eventName, callable|array $listener, int $priority = 0): void
{
$this->listeners[$eventName][$priority][] = $listener;
unset($this->sorted[$eventName], $this->optimized[$eventName]);
}

public function removeListener(string $eventName, callable|array $listener): void
{
if (empty($this->listeners[$eventName])) {
return;
}

if (\is_array($listener) && isset($listener[0]) && $listener[0] instanceof \Closure && 2 >= \count($listener)) {
$listener[0] = $listener[0]();
$listener[1] ??= '__invoke';
}

foreach ($this->listeners[$eventName] as $priority => &$listeners) {
foreach ($listeners as $k => &$v) {
if ($v !== $listener && \is_array($v) && isset($v[0]) && $v[0] instanceof \Closure && 2 >= \count($v)) {
$v[0] = $v[0]();
$v[1] ??= '__invoke';
}
if ($v === $listener || ($listener instanceof \Closure && $v == $listener)) {
unset($listeners[$k], $this->sorted[$eventName], $this->optimized[$eventName]);
}
}

if (!$listeners) {
unset($this->listeners[$eventName][$priority]);
}
}
}

public function addSubscriber(EventSubscriberInterface $subscriber): void
{
foreach ($subscriber->getSubscribedEvents() as $eventName => $params) {
if (\is_string($params)) {
$this->addListener($eventName, [$subscriber, $params]);
} elseif (\is_string($params[0])) {
$this->addListener($eventName, [$subscriber, $params[0]], $params[1] ?? 0);
} else {
foreach ($params as $listener) {
$this->addListener($eventName, [$subscriber, $listener[0]], $listener[1] ?? 0);
}
}
}
}

public function removeSubscriber(EventSubscriberInterface $subscriber): void
{
foreach ($subscriber->getSubscribedEvents() as $eventName => $params) {
if (\is_array($params) && \is_array($params[0])) {
foreach ($params as $listener) {
$this->removeListener($eventName, [$subscriber, $listener[0]]);
}
} else {
$this->removeListener($eventName, [$subscriber, \is_string($params) ? $params : $params[0]]);
}
}
}











protected function callListeners(iterable $listeners, string $eventName, object $event): void
{
$stoppable = $event instanceof StoppableEventInterface;

foreach ($listeners as $listener) {
if ($stoppable && $event->isPropagationStopped()) {
break;
}
$listener($event, $eventName, $this);
}
}




private function sortListeners(string $eventName): void
{
krsort($this->listeners[$eventName]);
$this->sorted[$eventName] = [];

foreach ($this->listeners[$eventName] as &$listeners) {
foreach ($listeners as &$listener) {
if (\is_array($listener) && isset($listener[0]) && $listener[0] instanceof \Closure && 2 >= \count($listener)) {
$listener[0] = $listener[0]();
$listener[1] ??= '__invoke';
}
$this->sorted[$eventName][] = $listener;
}
}
}




private function optimizeListeners(string $eventName): array
{
krsort($this->listeners[$eventName]);
$this->optimized[$eventName] = [];

foreach ($this->listeners[$eventName] as &$listeners) {
foreach ($listeners as &$listener) {
$closure = &$this->optimized[$eventName][];
if (\is_array($listener) && isset($listener[0]) && $listener[0] instanceof \Closure && 2 >= \count($listener)) {
$closure = static function (...$args) use (&$listener, &$closure) {
if ($listener[0] instanceof \Closure) {
$listener[0] = $listener[0]();
$listener[1] ??= '__invoke';
}
($closure = $listener(...))(...$args);
};
} else {
$closure = $listener instanceof WrappedListener ? $listener : $listener(...);
}
}
}

return $this->optimized[$eventName];
}
}
