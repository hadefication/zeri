<?php declare(strict_types=1);








namespace PHPUnit\Logging\TestDox;

use PHPUnit\Event\Test\DeprecationTriggered;
use PHPUnit\Event\Test\DeprecationTriggeredSubscriber;

/**
@no-named-arguments


*/
final readonly class TestTriggeredDeprecationSubscriber extends Subscriber implements DeprecationTriggeredSubscriber
{
public function notify(DeprecationTriggered $event): void
{
$this->collector()->testTriggeredDeprecation($event);
}
}
