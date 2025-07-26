<?php declare(strict_types=1);








namespace PHPUnit\TextUI\Output\Default\ProgressPrinter;

use PHPUnit\Event\Test\Finished;
use PHPUnit\Event\Test\FinishedSubscriber;

/**
@no-named-arguments


*/
final readonly class TestFinishedSubscriber extends Subscriber implements FinishedSubscriber
{
public function notify(Finished $event): void
{
$this->printer()->testFinished();
}
}
