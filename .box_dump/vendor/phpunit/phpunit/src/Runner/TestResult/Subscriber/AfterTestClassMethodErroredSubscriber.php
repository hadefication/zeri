<?php declare(strict_types=1);








namespace PHPUnit\TestRunner\TestResult;

use PHPUnit\Event\Test\AfterLastTestMethodErrored;
use PHPUnit\Event\Test\AfterLastTestMethodErroredSubscriber;

/**
@no-named-arguments


*/
final readonly class AfterTestClassMethodErroredSubscriber extends Subscriber implements AfterLastTestMethodErroredSubscriber
{
public function notify(AfterLastTestMethodErrored $event): void
{
$this->collector()->afterTestClassMethodErrored($event);
}
}
