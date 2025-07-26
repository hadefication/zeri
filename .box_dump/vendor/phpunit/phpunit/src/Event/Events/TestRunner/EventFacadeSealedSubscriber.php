<?php declare(strict_types=1);








namespace PHPUnit\Event\TestRunner;

use PHPUnit\Event\Subscriber;

/**
@no-named-arguments
*/
interface EventFacadeSealedSubscriber extends Subscriber
{
public function notify(EventFacadeSealed $event): void;
}
