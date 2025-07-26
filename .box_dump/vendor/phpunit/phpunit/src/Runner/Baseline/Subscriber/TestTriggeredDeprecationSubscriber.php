<?php declare(strict_types=1);








namespace PHPUnit\Runner\Baseline;

use PHPUnit\Event\Test\DeprecationTriggered;
use PHPUnit\Event\Test\DeprecationTriggeredSubscriber;
use PHPUnit\Runner\FileDoesNotExistException;

/**
@no-named-arguments


*/
final readonly class TestTriggeredDeprecationSubscriber extends Subscriber implements DeprecationTriggeredSubscriber
{




public function notify(DeprecationTriggered $event): void
{
$this->generator()->testTriggeredIssue($event);
}
}
