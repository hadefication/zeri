<?php










namespace Joli\JoliNotif\Driver;

use Joli\JoliNotif\Notification;







class NotifySendDriver extends AbstractCliBasedDriver
{
public function getBinary(): string
{
return 'notify-send';
}

public function getPriority(): int
{
return static::PRIORITY_MEDIUM;
}

protected function getCommandLineArguments(Notification $notification): array
{
$arguments = [];

if ($notification->getIcon()) {
$arguments[] = '--icon';
$arguments[] = $notification->getIcon();
}

if ($notification->getTitle()) {
$arguments[] = $notification->getTitle();
}

$arguments[] = $notification->getBody() ?? '';

return $arguments;
}
}
