<?php










namespace Joli\JoliNotif\Driver;

use Joli\JoliNotif\Notification;






class GrowlNotifyDriver extends AbstractCliBasedDriver
{
public function getBinary(): string
{
return 'growlnotify';
}

public function getPriority(): int
{
return static::PRIORITY_HIGH;
}

protected function getCommandLineArguments(Notification $notification): array
{
$arguments = [
'--message',
$notification->getBody() ?? '',
];

if ($notification->getTitle()) {
$arguments[] = '--title';
$arguments[] = $notification->getTitle();
}

if ($notification->getIcon()) {
$arguments[] = '--image';
$arguments[] = $notification->getIcon();
}

return $arguments;
}
}
