<?php declare(strict_types=1);








namespace PHPUnit\Logging\TestDox;

use PHPUnit\Event\Test\PhpDeprecationTriggered;
use PHPUnit\Event\Test\PhpDeprecationTriggeredSubscriber;

/**
@no-named-arguments


*/
final readonly class TestTriggeredPhpDeprecationSubscriber extends Subscriber implements PhpDeprecationTriggeredSubscriber
{
public function notify(PhpDeprecationTriggered $event): void
{
$this->collector()->testTriggeredPhpDeprecation($event);
}
}
