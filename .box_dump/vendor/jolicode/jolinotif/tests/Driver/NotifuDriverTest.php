<?php










namespace Joli\JoliNotif\tests\Driver;

use Joli\JoliNotif\Driver\DriverInterface;
use Joli\JoliNotif\Driver\NotifuDriver;

class NotifuDriverTest extends AbstractDriverTestCase
{
use AbstractCliBasedDriverTestTrait;
use BinaryProviderTestTrait;

private const BINARY = 'notifu';

public function testGetBinary()
{
$driver = $this->getDriver();

$this->assertSame(self::BINARY, $driver->getBinary());
}

public function testGetPriority()
{
$driver = $this->getDriver();

$this->assertSame(DriverInterface::PRIORITY_LOW, $driver->getPriority());
}

protected function getDriver(): NotifuDriver
{
return new NotifuDriver();
}

protected function getExpectedCommandLineForNotification(): string
{
return <<<'CLI'
            'notifu' '/m' 'I'\''m the notification body'
            CLI;
}

protected function getExpectedCommandLineForNotificationWithATitle(): string
{
return <<<'CLI'
            'notifu' '/m' 'I'\''m the notification body' '/p' 'I'\''m the notification title'
            CLI;
}

protected function getExpectedCommandLineForNotificationWithAnIcon(): string
{
$iconDir = $this->getIconDir();

return <<<CLI
            'notifu' '/m' 'I'\\''m the notification body' '/i' '{$iconDir}/image.gif'
            CLI;
}

protected function getExpectedCommandLineForNotificationWithAllOptions(): string
{
$iconDir = $this->getIconDir();

return <<<CLI
            'notifu' '/m' 'I'\\''m the notification body' '/p' 'I'\\''m the notification title' '/i' '{$iconDir}/image.gif'
            CLI;
}
}
