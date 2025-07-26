<?php










namespace Joli\JoliNotif\Driver;

use Joli\JoliNotif\Notification;
use JoliCode\PhpOsHelper\OsHelper;







class TerminalNotifierDriver extends AbstractCliBasedDriver
{
public function getBinary(): string
{
return 'terminal-notifier';
}

public function getPriority(): int
{
return static::PRIORITY_MEDIUM;
}

protected function getCommandLineArguments(Notification $notification): array
{
$arguments = [
'-message',
$notification->getBody() ?? '',
];

if ($notification->getTitle()) {
$arguments[] = '-title';
$arguments[] = $notification->getTitle();
}

if ($notification->getIcon() && version_compare(OsHelper::getMacOSVersion(), '10.9.0', '>=')) {
$arguments[] = '-contentImage';
$arguments[] = $notification->getIcon();
}

if ($notification->getOption('url')) {
$arguments[] = '-open';
$arguments[] = (string) $notification->getOption('url');
}

return $arguments;
}
}
