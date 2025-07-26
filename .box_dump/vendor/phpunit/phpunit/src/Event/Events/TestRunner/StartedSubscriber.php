<?php declare(strict_types=1);








namespace PHPUnit\Event\TestRunner;

use PHPUnit\Event\Subscriber;

/**
@no-named-arguments
*/
interface StartedSubscriber extends Subscriber
{
public function notify(Started $event): void;
}
