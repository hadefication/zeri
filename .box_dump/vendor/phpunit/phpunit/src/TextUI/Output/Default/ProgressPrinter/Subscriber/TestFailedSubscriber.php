<?php declare(strict_types=1);








namespace PHPUnit\TextUI\Output\Default\ProgressPrinter;

use PHPUnit\Event\Test\Failed;
use PHPUnit\Event\Test\FailedSubscriber;

/**
@no-named-arguments


*/
final readonly class TestFailedSubscriber extends Subscriber implements FailedSubscriber
{
public function notify(Failed $event): void
{
$this->printer()->testFailed();
}
}
