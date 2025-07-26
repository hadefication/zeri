<?php declare(strict_types=1);








namespace PHPUnit\TextUI\Output\Default\ProgressPrinter;

use PHPUnit\Event\Test\PhpWarningTriggered;
use PHPUnit\Event\Test\PhpWarningTriggeredSubscriber;

/**
@no-named-arguments


*/
final readonly class TestTriggeredPhpWarningSubscriber extends Subscriber implements PhpWarningTriggeredSubscriber
{
public function notify(PhpWarningTriggered $event): void
{
$this->printer()->testTriggeredPhpWarning($event);
}
}
