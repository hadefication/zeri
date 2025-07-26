<?php declare(strict_types=1);








namespace PHPUnit\Logging\TeamCity;

use PHPUnit\Event\InvalidArgumentException;
use PHPUnit\Event\TestSuite\Skipped;
use PHPUnit\Event\TestSuite\SkippedSubscriber;

/**
@no-named-arguments


*/
final readonly class TestSuiteSkippedSubscriber extends Subscriber implements SkippedSubscriber
{



public function notify(Skipped $event): void
{
$this->logger()->testSuiteSkipped($event);
}
}
