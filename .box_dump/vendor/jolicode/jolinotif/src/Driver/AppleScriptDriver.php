<?php










namespace Joli\JoliNotif\Driver;

use Joli\JoliNotif\Notification;
use JoliCode\PhpOsHelper\OsHelper;






class AppleScriptDriver extends AbstractCliBasedDriver
{
public function isSupported(): bool
{
if (OsHelper::isMacOS() && version_compare(OsHelper::getMacOSVersion(), '10.9.0', '>=')) {
return parent::isSupported();
}

return false;
}

public function getBinary(): string
{
return 'osascript';
}

public function getPriority(): int
{
return static::PRIORITY_LOW;
}

protected function getCommandLineArguments(Notification $notification): array
{
$script = 'display notification "' . str_replace('"', '\"', $notification->getBody() ?? '') . '"';

if ($notification->getTitle()) {
$script .= ' with title "' . str_replace('"', '\"', $notification->getTitle()) . '"';
}

if ($notification->getOption('subtitle')) {
$script .= ' subtitle "' . str_replace('"', '\"', (string) $notification->getOption('subtitle')) . '"';
}

if ($notification->getOption('sound')) {
$script .= ' sound name "' . str_replace('"', '\"', (string) $notification->getOption('sound')) . '"';
}

return [
'-e',
$script,
];
}
}
