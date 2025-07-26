<?php










namespace Joli\JoliNotif\Driver;

use Joli\JoliNotif\Exception\ExceptionInterface;
use Joli\JoliNotif\Exception\InvalidNotificationException;
use Joli\JoliNotif\Notification;




interface DriverInterface
{
public const PRIORITY_LOW = 0;
public const PRIORITY_MEDIUM = 50;
public const PRIORITY_HIGH = 100;





public function isSupported(): bool;




public function getPriority(): int;







public function send(Notification $notification): bool;
}
