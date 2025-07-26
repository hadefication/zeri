<?php declare(strict_types=1);








namespace PHPUnit\Runner\ResultCache;

use PHPUnit\Event\TestSuite\Finished;
use PHPUnit\Event\TestSuite\FinishedSubscriber;

/**
@no-named-arguments


*/
final readonly class TestSuiteFinishedSubscriber extends Subscriber implements FinishedSubscriber
{
public function notify(Finished $event): void
{
$this->handler()->testSuiteFinished();
}
}
