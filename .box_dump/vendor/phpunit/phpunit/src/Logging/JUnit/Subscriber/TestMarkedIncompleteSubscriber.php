<?php declare(strict_types=1);








namespace PHPUnit\Logging\JUnit;

use PHPUnit\Event\InvalidArgumentException;
use PHPUnit\Event\Test\MarkedIncomplete;
use PHPUnit\Event\Test\MarkedIncompleteSubscriber;

/**
@no-named-arguments


*/
final readonly class TestMarkedIncompleteSubscriber extends Subscriber implements MarkedIncompleteSubscriber
{



public function notify(MarkedIncomplete $event): void
{
$this->logger()->testMarkedIncomplete($event);
}
}
