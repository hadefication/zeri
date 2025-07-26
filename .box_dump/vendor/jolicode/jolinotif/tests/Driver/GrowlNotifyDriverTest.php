<?php










namespace Joli\JoliNotif\tests\Driver;

use Joli\JoliNotif\Driver\DriverInterface;
use Joli\JoliNotif\Driver\GrowlNotifyDriver;

class GrowlNotifyDriverTest extends AbstractDriverTestCase
{
use AbstractCliBasedDriverTestTrait;

private const BINARY = 'growlnotify';

public function testGetBinary()
{
$driver = $this->getDriver();

$this->assertSame(self::BINARY, $driver->getBinary());
}

public function testGetPriority()
{
$driver = $this->getDriver();

$this->assertSame(DriverInterface::PRIORITY_HIGH, $driver->getPriority());
}

protected function getDriver(): GrowlNotifyDriver
{
return new GrowlNotifyDriver();
}

protected function getExpectedCommandLineForNotification(): string
{
return <<<'CLI'
            'growlnotify' '--message' 'I'\''m the notification body'
            CLI;
}

protected function getExpectedCommandLineForNotificationWithATitle(): string
{
return <<<'CLI'
            'growlnotify' '--message' 'I'\''m the notification body' '--title' 'I'\''m the notification title'
            CLI;
}

protected function getExpectedCommandLineForNotificationWithAnIcon(): string
{
$iconDir = $this->getIconDir();

return <<<CLI
            'growlnotify' '--message' 'I'\\''m the notification body' '--image' '{$iconDir}/image.gif'
            CLI;
}

protected function getExpectedCommandLineForNotificationWithAllOptions(): string
{
$iconDir = $this->getIconDir();

return <<<CLI
            'growlnotify' '--message' 'I'\\''m the notification body' '--title' 'I'\\''m the notification title' '--image' '{$iconDir}/image.gif'
            CLI;
}
}
