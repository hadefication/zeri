<?php declare(strict_types=1);








namespace PHPUnit\TextUI\Output\Default\ProgressPrinter;

use PHPUnit\Event\Test\NoticeTriggered;
use PHPUnit\Event\Test\NoticeTriggeredSubscriber;

/**
@no-named-arguments


*/
final readonly class TestTriggeredNoticeSubscriber extends Subscriber implements NoticeTriggeredSubscriber
{
public function notify(NoticeTriggered $event): void
{
$this->printer()->testTriggeredNotice($event);
}
}
