<?php










namespace Joli\JoliNotif\Driver;

use Joli\JoliNotif\Notification;
use JoliCode\PhpOsHelper\OsHelper;
use Symfony\Component\Process\Process;







class NotifuDriver extends AbstractCliBasedDriver implements BinaryProviderInterface
{
public function getBinary(): string
{
return 'notifu';
}

public function getPriority(): int
{
return static::PRIORITY_LOW;
}

public function canBeUsed(): bool
{
return OsHelper::isWindows() && OsHelper::isWindowsSeven();
}

public function getRootDir(): string
{
return \dirname(__DIR__, 2) . '/bin/notifu';
}

public function getEmbeddedBinary(): string
{
return 'notifu.exe';
}

public function getExtraFiles(): array
{
return [];
}

protected function getCommandLineArguments(Notification $notification): array
{
$arguments = [
'/m',
$notification->getBody() ?? '',
];

if ($notification->getTitle()) {
$arguments[] = '/p';
$arguments[] = $notification->getTitle();
}

if ($notification->getIcon()) {
$arguments[] = '/i';
$arguments[] = $notification->getIcon();
}

return $arguments;
}

protected function launchProcess(Process $process): void
{
$process->start();
}

protected function handleExitCode(Process $process): bool
{
return true;
}
}
