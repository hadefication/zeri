<?php declare(strict_types=1);








namespace PHPUnit\Runner\DeprecationCollector;

use PHPUnit\Event\Test\Prepared;
use PHPUnit\Event\Test\PreparedSubscriber;

/**
@no-named-arguments


*/
final class TestPreparedSubscriber extends Subscriber implements PreparedSubscriber
{
public function notify(Prepared $event): void
{
$this->collector()->testPrepared();
}
}
