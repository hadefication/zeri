<?php declare(strict_types=1);








namespace PHPUnit\TextUI\Output\Default\ProgressPrinter;

use PHPUnit\Event\TestRunner\ExecutionStarted;
use PHPUnit\Event\TestRunner\ExecutionStartedSubscriber;

/**
@no-named-arguments


*/
final readonly class TestRunnerExecutionStartedSubscriber extends Subscriber implements ExecutionStartedSubscriber
{
public function notify(ExecutionStarted $event): void
{
$this->printer()->testRunnerExecutionStarted($event);
}
}
