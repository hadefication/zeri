<?php

namespace Illuminate\Support\Facades;

use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Notifications\ChannelManager;
use Illuminate\Support\Testing\Fakes\NotificationFake;




































class Notification extends Facade
{





public static function fake()
{
return tap(new NotificationFake, function ($fake) {
static::swap($fake);
});
}







public static function routes(array $channels)
{
$notifiable = new AnonymousNotifiable;

foreach ($channels as $channel => $route) {
$notifiable->route($channel, $route);
}

return $notifiable;
}








public static function route($channel, $route)
{
return (new AnonymousNotifiable)->route($channel, $route);
}






protected static function getFacadeAccessor()
{
return ChannelManager::class;
}
}
