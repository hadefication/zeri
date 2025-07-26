<?php declare(strict_types=1);








namespace PHPUnit\TextUI\Output\Default\ProgressPrinter;

use PHPUnit\Event\Test\PhpDeprecationTriggered;
use PHPUnit\Event\Test\PhpDeprecationTriggeredSubscriber;

/**
@no-named-arguments


*/
final readonly class TestTriggeredPhpDeprecationSubscriber extends Subscriber implements PhpDeprecationTriggeredSubscriber
{
public function notify(PhpDeprecationTriggered $event): void
{
$this->printer()->testTriggeredPhpDeprecation($event);
}
}
