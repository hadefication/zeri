<?php










namespace NunoMaduro\LaravelDesktopNotifier;

use Illuminate\Console\Command;
use Illuminate\Support\ServiceProvider;
use Joli\JoliNotif\NotifierFactory;
use NunoMaduro\LaravelDesktopNotifier\Contracts\Notification as NotificationContract;
use NunoMaduro\LaravelDesktopNotifier\Contracts\Notifier as NotifierContract;




class LaravelDesktopNotifierServiceProvider extends ServiceProvider
{



public function boot()
{









Command::macro('notify', function (string $text, string $body, $icon = null) {
$notifier = $this->laravel[Contracts\Notifier::class];

$notification = $this->laravel[Contracts\Notification::class]
->setTitle($text)
->setBody($body);

if (! empty($icon)) {
$notification->setIcon($icon);
}

$notifier->send($notification);
});
}




public function register()
{
$this->app->singleton('desktop.notifier', function ($app) {
$config = $app['config']['app.notifiers'];

$notifier = NotifierFactory::create(is_array($config) ? $config : []);

return new Notifier($notifier);
});

$this->app->alias('desktop.notifier', NotifierContract::class);

$this->app->bind('desktop.notification', function () {
return new Notification();
});

$this->app->alias('desktop.notification', NotificationContract::class);
}
}
