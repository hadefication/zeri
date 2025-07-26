<?php declare(strict_types=1);








namespace PHPUnit\Logging\JUnit;

use PHPUnit\Event\InvalidArgumentException;
use PHPUnit\Event\Test\PreparationStarted;
use PHPUnit\Event\Test\PreparationStartedSubscriber;

/**
@no-named-arguments


*/
final readonly class TestPreparationStartedSubscriber extends Subscriber implements PreparationStartedSubscriber
{



public function notify(PreparationStarted $event): void
{
$this->logger()->testPreparationStarted($event);
}
}
