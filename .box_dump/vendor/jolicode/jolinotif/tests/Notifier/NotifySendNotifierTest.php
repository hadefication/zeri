<?php










namespace Joli\JoliNotif\tests\Notifier;

use Joli\JoliNotif\Notifier;
use Joli\JoliNotif\Notifier\NotifySendNotifier;

/**
@group
*/
class NotifySendNotifierTest extends NotifierTestCase
{
use CliBasedNotifierTestTrait;

private const BINARY = 'notify-send';

public function testGetBinary()
{
$notifier = $this->getNotifier();

$this->assertSame(self::BINARY, $notifier->getBinary());
}

public function testGetPriority()
{
$notifier = $this->getNotifier();

$this->assertSame(Notifier::PRIORITY_MEDIUM, $notifier->getPriority());
}

protected function getNotifier(): Notifier
{
return new NotifySendNotifier();
}

protected function getExpectedCommandLineForNotification(): string
{
return <<<'CLI'
            'notify-send' 'I'\''m the notification body'
            CLI;
}

protected function getExpectedCommandLineForNotificationWithATitle(): string
{
return <<<'CLI'
            'notify-send' 'I'\''m the notification title' 'I'\''m the notification body'
            CLI;
}

protected function getExpectedCommandLineForNotificationWithAnIcon(): string
{
$iconDir = $this->getIconDir();

return <<<CLI
            'notify-send' '--icon' '{$iconDir}/image.gif' 'I'\\''m the notification body'
            CLI;
}

protected function getExpectedCommandLineForNotificationWithAllOptions(): string
{
$iconDir = $this->getIconDir();

return <<<CLI
            'notify-send' '--icon' '{$iconDir}/image.gif' 'I'\\''m the notification title' 'I'\\''m the notification body'
            CLI;
}
}
