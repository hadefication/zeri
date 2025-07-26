<?php declare(strict_types=1);








namespace PHPUnit\Logging\JUnit;

use PHPUnit\Event\Test\PrintedUnexpectedOutput;
use PHPUnit\Event\Test\PrintedUnexpectedOutputSubscriber;

/**
@no-named-arguments


*/
final readonly class TestPrintedUnexpectedOutputSubscriber extends Subscriber implements PrintedUnexpectedOutputSubscriber
{
public function notify(PrintedUnexpectedOutput $event): void
{
$this->logger()->testPrintedUnexpectedOutput($event);
}
}
