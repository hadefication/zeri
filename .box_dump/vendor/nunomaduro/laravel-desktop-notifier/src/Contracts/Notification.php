<?php










namespace NunoMaduro\LaravelDesktopNotifier\Contracts;






interface Notification
{



public function getTitle();




public function setTitle(string $title): \Joli\JoliNotif\Notification;




public function getBody();




public function setBody(string $body): \Joli\JoliNotif\Notification;




public function getIcon();




public function setIcon(string $icon): \Joli\JoliNotif\Notification;
}
