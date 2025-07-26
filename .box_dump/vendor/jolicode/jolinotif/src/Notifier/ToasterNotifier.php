<?php










namespace Joli\JoliNotif\Notifier;

use Joli\JoliNotif\Notification;
use Joli\JoliNotif\Notifier;
use JoliCode\PhpOsHelper\OsHelper;

trigger_deprecation('jolicode/jolinotif', '2.3', 'The "%s" class is deprecated and will be removed in 3.0.', ToasterNotifier::class);







class ToasterNotifier extends CliBasedNotifier implements BinaryProvider
{
public function getBinary(): string
{
return 'toast';
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
return \dirname(__DIR__, 2) . '/bin/toaster';
}

public function getEmbeddedBinary(): string
{
return 'toast.exe';
}

public function getExtraFiles(): array
{
return [
'Microsoft.WindowsAPICodePack.dll',
'Microsoft.WindowsAPICodePack.Shell.dll',
];
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
}
