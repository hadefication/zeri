<?php declare(strict_types=1);








namespace PHPUnit\TestRunner\TestResult;

use PHPUnit\Event\TestSuite\Finished;
use PHPUnit\Event\TestSuite\FinishedSubscriber;

/**
@no-named-arguments


*/
final readonly class TestSuiteFinishedSubscriber extends Subscriber implements FinishedSubscriber
{
public function notify(Finished $event): void
{
$this->collector()->testSuiteFinished($event);
}
}
