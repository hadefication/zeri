<?php declare(strict_types=1);








namespace PHPUnit\Logging\JUnit;

use PHPUnit\Event\TestRunner\ExecutionFinished;
use PHPUnit\Event\TestRunner\ExecutionFinishedSubscriber;

/**
@no-named-arguments


*/
final readonly class TestRunnerExecutionFinishedSubscriber extends Subscriber implements ExecutionFinishedSubscriber
{
public function notify(ExecutionFinished $event): void
{
$this->logger()->flush();
}
}
