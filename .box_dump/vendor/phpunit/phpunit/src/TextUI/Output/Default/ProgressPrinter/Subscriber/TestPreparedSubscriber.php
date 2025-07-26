<?php declare(strict_types=1);








namespace PHPUnit\TextUI\Output\Default\ProgressPrinter;

use PHPUnit\Event\Test\Prepared;
use PHPUnit\Event\Test\PreparedSubscriber;

/**
@no-named-arguments


*/
final readonly class TestPreparedSubscriber extends Subscriber implements PreparedSubscriber
{
public function notify(Prepared $event): void
{
$this->printer()->testPrepared();
}
}
