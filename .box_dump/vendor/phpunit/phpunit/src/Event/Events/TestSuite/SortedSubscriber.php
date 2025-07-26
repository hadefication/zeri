<?php declare(strict_types=1);








namespace PHPUnit\Event\TestSuite;

use PHPUnit\Event\Subscriber;

/**
@no-named-arguments
*/
interface SortedSubscriber extends Subscriber
{
public function notify(Sorted $event): void;
}
