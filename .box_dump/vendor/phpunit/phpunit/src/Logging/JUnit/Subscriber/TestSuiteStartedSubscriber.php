<?php declare(strict_types=1);








namespace PHPUnit\Logging\JUnit;

use PHPUnit\Event\TestSuite\Started;
use PHPUnit\Event\TestSuite\StartedSubscriber;

/**
@no-named-arguments


*/
final readonly class TestSuiteStartedSubscriber extends Subscriber implements StartedSubscriber
{
public function notify(Started $event): void
{
$this->logger()->testSuiteStarted($event);
}
}
