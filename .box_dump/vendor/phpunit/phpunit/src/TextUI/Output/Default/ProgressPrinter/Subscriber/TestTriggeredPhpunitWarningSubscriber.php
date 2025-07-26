<?php declare(strict_types=1);








namespace PHPUnit\TextUI\Output\Default\ProgressPrinter;

use PHPUnit\Event\Test\PhpunitWarningTriggered;
use PHPUnit\Event\Test\PhpunitWarningTriggeredSubscriber;

/**
@no-named-arguments


*/
final readonly class TestTriggeredPhpunitWarningSubscriber extends Subscriber implements PhpunitWarningTriggeredSubscriber
{
public function notify(PhpunitWarningTriggered $event): void
{
$this->printer()->testTriggeredPhpunitWarning();
}
}
