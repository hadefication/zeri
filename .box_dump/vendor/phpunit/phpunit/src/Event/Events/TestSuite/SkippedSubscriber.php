<?php declare(strict_types=1);








namespace PHPUnit\Event\TestSuite;

use PHPUnit\Event\Subscriber;

/**
@no-named-arguments
*/
interface SkippedSubscriber extends Subscriber
{
public function notify(Skipped $event): void;
}
