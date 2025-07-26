<?php declare(strict_types=1);








namespace PHPUnit\Runner\GarbageCollection;

use PHPUnit\Event\InvalidArgumentException;
use PHPUnit\Event\TestRunner\ExecutionStarted;
use PHPUnit\Event\TestRunner\ExecutionStartedSubscriber as TestRunnerExecutionStartedSubscriber;

/**
@no-named-arguments


*/
final readonly class ExecutionStartedSubscriber extends Subscriber implements TestRunnerExecutionStartedSubscriber
{




public function notify(ExecutionStarted $event): void
{
$this->handler()->executionStarted();
}
}
