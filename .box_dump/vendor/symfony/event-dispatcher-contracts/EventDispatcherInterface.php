<?php










namespace Symfony\Contracts\EventDispatcher;

use Psr\EventDispatcher\EventDispatcherInterface as PsrEventDispatcherInterface;




interface EventDispatcherInterface extends PsrEventDispatcherInterface
{
/**
@template








*/
public function dispatch(object $event, ?string $eventName = null): object;
}
