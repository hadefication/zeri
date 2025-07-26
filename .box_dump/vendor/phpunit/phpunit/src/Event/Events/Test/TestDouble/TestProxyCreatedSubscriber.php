<?php declare(strict_types=1);








namespace PHPUnit\Event\Test;

use PHPUnit\Event\Subscriber;

/**
@no-named-arguments
*/
interface TestProxyCreatedSubscriber extends Subscriber
{
public function notify(TestProxyCreated $event): void;
}
