<?php










namespace NunoMaduro\LaravelDesktopNotifier\Facades;

use Illuminate\Support\Facades\Facade;






class Notifier extends Facade
{





protected static function getFacadeAccessor()
{
return 'desktop.notifier';
}
}
