<?php declare(strict_types=1);








namespace PHPUnit\Logging\TeamCity;

use PHPUnit\Event\InvalidArgumentException;
use PHPUnit\Event\Test\Errored;
use PHPUnit\Event\Test\ErroredSubscriber;

/**
@no-named-arguments


*/
final readonly class TestErroredSubscriber extends Subscriber implements ErroredSubscriber
{



public function notify(Errored $event): void
{
$this->logger()->testErrored($event);
}
}
