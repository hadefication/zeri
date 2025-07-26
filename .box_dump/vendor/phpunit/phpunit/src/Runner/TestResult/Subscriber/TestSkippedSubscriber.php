<?php declare(strict_types=1);








namespace PHPUnit\TestRunner\TestResult;

use PHPUnit\Event\Test\Skipped;
use PHPUnit\Event\Test\SkippedSubscriber;

/**
@no-named-arguments


*/
final readonly class TestSkippedSubscriber extends Subscriber implements SkippedSubscriber
{
public function notify(Skipped $event): void
{
$this->collector()->testSkipped($event);
}
}
