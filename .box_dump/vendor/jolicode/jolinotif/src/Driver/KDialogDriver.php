<?php










namespace Joli\JoliNotif\Driver;

use Joli\JoliNotif\Notification;







class KDialogDriver extends AbstractCliBasedDriver
{
public function getBinary(): string
{
return 'kdialog';
}

public function getPriority(): int
{
return static::PRIORITY_HIGH;
}

protected function getCommandLineArguments(Notification $notification): array
{
$arguments = [];

if ($notification->getTitle()) {
$arguments[] = '--title';
$arguments[] = $notification->getTitle();
}

$arguments[] = '--passivepopup';
$arguments[] = $notification->getBody() ?? '';


$arguments[] = 5;

return $arguments;
}
}
