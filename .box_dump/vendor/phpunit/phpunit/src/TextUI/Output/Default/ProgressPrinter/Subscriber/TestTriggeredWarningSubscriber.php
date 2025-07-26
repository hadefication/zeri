<?php declare(strict_types=1);








namespace PHPUnit\TextUI\Output\Default\ProgressPrinter;

use PHPUnit\Event\Test\WarningTriggered;
use PHPUnit\Event\Test\WarningTriggeredSubscriber;

/**
@no-named-arguments


*/
final readonly class TestTriggeredWarningSubscriber extends Subscriber implements WarningTriggeredSubscriber
{
public function notify(WarningTriggered $event): void
{
$this->printer()->testTriggeredWarning($event);
}
}
