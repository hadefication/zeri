<?php










namespace Joli\JoliNotif\tests\Notifier;

use Joli\JoliNotif\Notifier;

/**
@group
*/
class KDialogNotifierTest extends NotifierTestCase
{
use CliBasedNotifierTestTrait;

private const BINARY = 'kdialog';

public function testGetBinary()
{
$notifier = $this->getNotifier();

$this->assertSame(self::BINARY, $notifier->getBinary());
}

public function testGetPriority()
{
$notifier = $this->getNotifier();

$this->assertSame(Notifier::PRIORITY_HIGH, $notifier->getPriority());
}

protected function getNotifier(): Notifier
{
return new Notifier\KDialogNotifier();
}

protected function getExpectedCommandLineForNotification(): string
{
return <<<'CLI'
            'kdialog' '--passivepopup' 'I'\''m the notification body' '5'
            CLI;
}

protected function getExpectedCommandLineForNotificationWithATitle(): string
{
return <<<'CLI'
            'kdialog' '--title' 'I'\''m the notification title' '--passivepopup' 'I'\''m the notification body' '5'
            CLI;
}

protected function getExpectedCommandLineForNotificationWithAnIcon(): string
{
return <<<'CLI'
            'kdialog' '--passivepopup' 'I'\''m the notification body' '5'
            CLI;
}

protected function getExpectedCommandLineForNotificationWithAllOptions(): string
{
return <<<'CLI'
            'kdialog' '--title' 'I'\''m the notification title' '--passivepopup' 'I'\''m the notification body' '5'
            CLI;
}
}
