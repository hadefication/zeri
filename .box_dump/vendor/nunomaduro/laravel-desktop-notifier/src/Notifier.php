<?php










namespace NunoMaduro\LaravelDesktopNotifier;

use Joli\JoliNotif\Notifier as BaseNotifier;
use NunoMaduro\LaravelDesktopNotifier\Contracts\Notifier as NotifierContract;






class Notifier implements NotifierContract
{



protected $notifier;




public function __construct(BaseNotifier $notifier)
{
$this->notifier = $notifier;
}




public function isSupported(): bool
{
return $this->notifier->isSupported();
}




public function getPriority(): int
{
return $this->notifier->getPriority();
}




public function send(\Joli\JoliNotif\Notification $notification): bool
{
return $this->notifier->send($notification);
}
}
