<?php declare(strict_types=1);








namespace PHPUnit\Runner\Baseline;

use PHPUnit\Event\Test\WarningTriggered;
use PHPUnit\Event\Test\WarningTriggeredSubscriber;
use PHPUnit\Runner\FileDoesNotExistException;

/**
@no-named-arguments


*/
final readonly class TestTriggeredWarningSubscriber extends Subscriber implements WarningTriggeredSubscriber
{




public function notify(WarningTriggered $event): void
{
$this->generator()->testTriggeredIssue($event);
}
}
