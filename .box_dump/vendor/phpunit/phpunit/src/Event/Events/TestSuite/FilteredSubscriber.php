<?php declare(strict_types=1);








namespace PHPUnit\Event\TestSuite;

use PHPUnit\Event\Subscriber;

/**
@no-named-arguments
*/
interface FilteredSubscriber extends Subscriber
{
public function notify(Filtered $event): void;
}
