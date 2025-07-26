<?php declare(strict_types=1);








namespace PHPUnit\TestRunner\TestResult;

use PHPUnit\Event\TestRunner\ExecutionStarted;
use PHPUnit\Event\TestRunner\ExecutionStartedSubscriber as TestRunnerExecutionStartedSubscriber;

/**
@no-named-arguments


*/
final readonly class ExecutionStartedSubscriber extends Subscriber implements TestRunnerExecutionStartedSubscriber
{
public function notify(ExecutionStarted $event): void
{
$this->collector()->executionStarted($event);
}
}
