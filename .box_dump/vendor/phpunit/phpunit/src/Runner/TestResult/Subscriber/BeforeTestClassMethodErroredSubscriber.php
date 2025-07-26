<?php declare(strict_types=1);








namespace PHPUnit\TestRunner\TestResult;

use PHPUnit\Event\Test\BeforeFirstTestMethodErrored;
use PHPUnit\Event\Test\BeforeFirstTestMethodErroredSubscriber;

/**
@no-named-arguments


*/
final readonly class BeforeTestClassMethodErroredSubscriber extends Subscriber implements BeforeFirstTestMethodErroredSubscriber
{
public function notify(BeforeFirstTestMethodErrored $event): void
{
$this->collector()->beforeTestClassMethodErrored($event);
}
}
