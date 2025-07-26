<?php declare(strict_types=1);








namespace PHPUnit\Event\TestRunner;

use PHPUnit\Event\Subscriber;

/**
@no-named-arguments
*/
interface ExecutionStartedSubscriber extends Subscriber
{
public function notify(ExecutionStarted $event): void;
}
