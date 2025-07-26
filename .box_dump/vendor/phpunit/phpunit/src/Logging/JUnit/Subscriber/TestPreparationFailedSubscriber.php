<?php declare(strict_types=1);








namespace PHPUnit\Logging\JUnit;

use PHPUnit\Event\InvalidArgumentException;
use PHPUnit\Event\Test\PreparationFailed;
use PHPUnit\Event\Test\PreparationFailedSubscriber;

/**
@no-named-arguments


*/
final readonly class TestPreparationFailedSubscriber extends Subscriber implements PreparationFailedSubscriber
{



public function notify(PreparationFailed $event): void
{
$this->logger()->testPreparationFailed();
}
}
