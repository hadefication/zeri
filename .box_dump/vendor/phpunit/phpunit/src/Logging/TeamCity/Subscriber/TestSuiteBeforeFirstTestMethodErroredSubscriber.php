<?php declare(strict_types=1);








namespace PHPUnit\Logging\TeamCity;

use PHPUnit\Event\InvalidArgumentException;
use PHPUnit\Event\Test\BeforeFirstTestMethodErrored;
use PHPUnit\Event\Test\BeforeFirstTestMethodErroredSubscriber;

/**
@no-named-arguments


*/
final readonly class TestSuiteBeforeFirstTestMethodErroredSubscriber extends Subscriber implements BeforeFirstTestMethodErroredSubscriber
{



public function notify(BeforeFirstTestMethodErrored $event): void
{
$this->logger()->beforeFirstTestMethodErrored($event);
}
}
