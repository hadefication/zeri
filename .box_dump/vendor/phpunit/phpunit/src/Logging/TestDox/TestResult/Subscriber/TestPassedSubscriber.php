<?php declare(strict_types=1);








namespace PHPUnit\Logging\TestDox;

use PHPUnit\Event\Test\Passed;
use PHPUnit\Event\Test\PassedSubscriber;

/**
@no-named-arguments


*/
final readonly class TestPassedSubscriber extends Subscriber implements PassedSubscriber
{
public function notify(Passed $event): void
{
$this->collector()->testPassed($event);
}
}
