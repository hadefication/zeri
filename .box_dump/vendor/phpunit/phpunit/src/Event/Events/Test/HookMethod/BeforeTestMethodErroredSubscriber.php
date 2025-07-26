<?php declare(strict_types=1);








namespace PHPUnit\Event\Test;

use PHPUnit\Event\Subscriber;

/**
@no-named-arguments
*/
interface BeforeTestMethodErroredSubscriber extends Subscriber
{
public function notify(BeforeTestMethodErrored $event): void;
}
