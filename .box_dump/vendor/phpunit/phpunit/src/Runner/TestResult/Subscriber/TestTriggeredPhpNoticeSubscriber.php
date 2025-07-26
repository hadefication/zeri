<?php declare(strict_types=1);








namespace PHPUnit\TestRunner\TestResult;

use PHPUnit\Event\Test\PhpNoticeTriggered;
use PHPUnit\Event\Test\PhpNoticeTriggeredSubscriber;

/**
@no-named-arguments


*/
final readonly class TestTriggeredPhpNoticeSubscriber extends Subscriber implements PhpNoticeTriggeredSubscriber
{
public function notify(PhpNoticeTriggered $event): void
{
$this->collector()->testTriggeredPhpNotice($event);
}
}
