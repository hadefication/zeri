<?php declare(strict_types=1);








namespace PHPUnit\Event\Test;

use PHPUnit\Event\Subscriber;

/**
@no-named-arguments
*/
interface MarkedIncompleteSubscriber extends Subscriber
{
public function notify(MarkedIncomplete $event): void;
}
