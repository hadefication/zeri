<?php declare(strict_types=1);








namespace PHPUnit\Event;

/**
@no-named-arguments


*/
interface Dispatcher
{



public function dispatch(Event $event): void;
}
