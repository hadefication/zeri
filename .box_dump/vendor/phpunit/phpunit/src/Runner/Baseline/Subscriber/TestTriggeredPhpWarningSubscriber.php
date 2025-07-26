<?php declare(strict_types=1);








namespace PHPUnit\Runner\Baseline;

use PHPUnit\Event\Test\PhpWarningTriggered;
use PHPUnit\Event\Test\PhpWarningTriggeredSubscriber;
use PHPUnit\Runner\FileDoesNotExistException;

/**
@no-named-arguments


*/
final readonly class TestTriggeredPhpWarningSubscriber extends Subscriber implements PhpWarningTriggeredSubscriber
{




public function notify(PhpWarningTriggered $event): void
{
$this->generator()->testTriggeredIssue($event);
}
}
