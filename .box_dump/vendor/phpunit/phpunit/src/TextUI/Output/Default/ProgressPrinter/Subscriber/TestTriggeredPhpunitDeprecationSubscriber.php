<?php declare(strict_types=1);








namespace PHPUnit\TextUI\Output\Default\ProgressPrinter;

use PHPUnit\Event\Test\PhpunitDeprecationTriggered;
use PHPUnit\Event\Test\PhpunitDeprecationTriggeredSubscriber;

/**
@no-named-arguments


*/
final readonly class TestTriggeredPhpunitDeprecationSubscriber extends Subscriber implements PhpunitDeprecationTriggeredSubscriber
{
public function notify(PhpunitDeprecationTriggered $event): void
{
$this->printer()->testTriggeredPhpunitDeprecation();
}
}
