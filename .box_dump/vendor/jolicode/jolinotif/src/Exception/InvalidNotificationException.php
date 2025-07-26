<?php










namespace Joli\JoliNotif\Exception;

use Joli\JoliNotif\Notification;

class InvalidNotificationException extends \LogicException implements ExceptionInterface
{
private Notification $notification;

public function __construct(
Notification $notification,
string $message = '',
int $code = 0,
?\Throwable $previous = null,
) {
$this->notification = $notification;

parent::__construct($message, $code, $previous);
}

public function getNotification(): Notification
{
return $this->notification;
}
}
