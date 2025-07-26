<?php










namespace Joli\JoliNotif;

interface NotifierInterface
{





public function send(Notification $notification): bool;
}
