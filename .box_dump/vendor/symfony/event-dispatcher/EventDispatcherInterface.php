<?php










namespace Symfony\Component\EventDispatcher;

use Symfony\Contracts\EventDispatcher\EventDispatcherInterface as ContractsEventDispatcherInterface;








interface EventDispatcherInterface extends ContractsEventDispatcherInterface
{






public function addListener(string $eventName, callable $listener, int $priority = 0): void;







public function addSubscriber(EventSubscriberInterface $subscriber): void;




public function removeListener(string $eventName, callable $listener): void;

public function removeSubscriber(EventSubscriberInterface $subscriber): void;






public function getListeners(?string $eventName = null): array;






public function getListenerPriority(string $eventName, callable $listener): ?int;




public function hasListeners(?string $eventName = null): bool;
}
