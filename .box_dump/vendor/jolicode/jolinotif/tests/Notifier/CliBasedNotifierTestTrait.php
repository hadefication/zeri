<?php










namespace Joli\JoliNotif\tests\Notifier;

use Joli\JoliNotif\Notification;
use Joli\JoliNotif\Util\OsHelper;
use Symfony\Component\Process\Process;





trait CliBasedNotifierTestTrait
{
public function testIsSupported()
{
if (OsHelper::isUnix()) {
$commandLine = 'command -v ' . static::BINARY . ' >/dev/null 2>&1';
} else {
$commandLine = 'where ' . static::BINARY;
}

passthru($commandLine, $return);
$supported = 0 === $return;

$this->assertSame($supported, $this->getNotifier()->isSupported());
}

/**
@dataProvider
*/
public function testConfigureProcessAcceptAnyValidNotification(Notification $notification, string $expectedCommandLine)
{
try {
$arguments = $this->invokeMethod($this->getNotifier(), 'getCommandLineArguments', [$notification]);

$this->assertSame($expectedCommandLine, (new Process(array_merge([self::BINARY], $arguments)))->getCommandLine());
} catch (\Exception $e) {
$this->fail($e->getMessage());
}
}

public function provideValidNotifications(): array
{
$iconDir = $this->getIconDir();

return [
[
(new Notification())
->setBody('I\'m the notification body'),
$this->getExpectedCommandLineForNotification(),
],
[
(new Notification())
->setBody('I\'m the notification body')
->setTitle('I\'m the notification title'),
$this->getExpectedCommandLineForNotificationWithATitle(),
],
[
(new Notification())
->setBody('I\'m the notification body')
->addOption('subtitle', 'I\'m the notification subtitle'),
$this->getExpectedCommandLineForNotificationWithASubtitle(),
],
[
(new Notification())
->setBody('I\'m the notification body')
->addOption('sound', 'Frog'),
$this->getExpectedCommandLineForNotificationWithASound(),
],
[
(new Notification())
->setBody('I\'m the notification body')
->addOption('url', 'https://google.com'),
$this->getExpectedCommandLineForNotificationWithAnUrl(),
],
[
(new Notification())
->setBody('I\'m the notification body')
->setIcon($iconDir . '/image.gif'),
$this->getExpectedCommandLineForNotificationWithAnIcon(),
],
[
(new Notification())
->setBody('I\'m the notification body')
->setTitle('I\'m the notification title')
->addOption('subtitle', 'I\'m the notification subtitle')
->addOption('sound', 'Frog')
->addOption('url', 'https://google.com')
->setIcon($iconDir . '/image.gif'),
$this->getExpectedCommandLineForNotificationWithAllOptions(),
],
];
}

public function testSendThrowsExceptionWhenNotificationDoesntHaveBody()
{
$notifier = $this->getNotifier();

$notification = new Notification();

try {
$notifier->send($notification);
$this->fail('Expected a InvalidNotificationException');
} catch (\Exception $e) {
$this->assertInstanceOf('Joli\JoliNotif\Exception\InvalidNotificationException', $e);
}
}

public function testSendThrowsExceptionWhenNotificationHasAnEmptyBody()
{
$notifier = $this->getNotifier();

$notification = new Notification();
$notification->setBody('');

try {
$notifier->send($notification);
$this->fail('Expected a InvalidNotificationException');
} catch (\Exception $e) {
$this->assertInstanceOf('Joli\JoliNotif\Exception\InvalidNotificationException', $e);
}
}

abstract protected function getExpectedCommandLineForNotification(): string;

abstract protected function getExpectedCommandLineForNotificationWithATitle(): string;




protected function getExpectedCommandLineForNotificationWithASubtitle(): string
{
return $this->getExpectedCommandLineForNotification();
}




protected function getExpectedCommandLineForNotificationWithASound(): string
{
return $this->getExpectedCommandLineForNotification();
}




protected function getExpectedCommandLineForNotificationWithAnUrl(): string
{
return $this->getExpectedCommandLineForNotification();
}

abstract protected function getExpectedCommandLineForNotificationWithAnIcon(): string;

abstract protected function getExpectedCommandLineForNotificationWithAllOptions(): string;
}
