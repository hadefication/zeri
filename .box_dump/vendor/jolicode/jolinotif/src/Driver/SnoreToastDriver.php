<?php










namespace Joli\JoliNotif\Driver;

use Joli\JoliNotif\Notification;
use JoliCode\PhpOsHelper\OsHelper;
use Symfony\Component\Process\Process;







class SnoreToastDriver extends AbstractCliBasedDriver implements BinaryProviderInterface
{
public function getBinary(): string
{
return 'snoretoast';
}

public function getPriority(): int
{
return static::PRIORITY_MEDIUM;
}

public function canBeUsed(): bool
{
return
(OsHelper::isWindows() && OsHelper::isWindowsEightOrHigher())
|| OsHelper::isWindowsSubsystemForLinux();
}

public function getRootDir(): string
{
return \dirname(__DIR__, 2) . '/bin/snoreToast';
}

public function getEmbeddedBinary(): string
{
return 'snoretoast-x86.exe';
}

public function getExtraFiles(): array
{
return [];
}

protected function getCommandLineArguments(Notification $notification): array
{
$arguments = [
'-m',
$notification->getBody() ?? '',
];

if ($notification->getTitle()) {
$arguments[] = '-t';
$arguments[] = $notification->getTitle();
}

if ($notification->getIcon()) {
$arguments[] = '-p';
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
