<?php

declare(strict_types=1);










namespace LaravelZero\Framework\Bootstrap;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Bootstrap\RegisterProviders as BaseRegisterProviders;
use LaravelZero\Framework\Application;
use LaravelZero\Framework\Components;
use LaravelZero\Framework\Contracts\BootstrapperContract;
use LaravelZero\Framework\Kernel as ConsoleKernel;
use LaravelZero\Framework\Providers;
use LaravelZero\Framework\Providers\Collision\CollisionServiceProvider;
use LaravelZero\Framework\Providers\CommandRecorder\CommandRecorderServiceProvider;
use LaravelZero\Framework\Providers\NullLogger\NullLoggerServiceProvider;
use NunoMaduro\LaravelConsoleSummary\LaravelConsoleSummaryServiceProvider;
use NunoMaduro\LaravelConsoleTask\LaravelConsoleTaskServiceProvider;
use NunoMaduro\LaravelDesktopNotifier\LaravelDesktopNotifierServiceProvider;

use function collect;




final class RegisterProviders implements BootstrapperContract
{





protected $providers = [
NullLoggerServiceProvider::class,
CollisionServiceProvider::class,
Providers\Cache\CacheServiceProvider::class,
Providers\Filesystem\FilesystemServiceProvider::class,
Providers\Composer\ComposerServiceProvider::class,
LaravelDesktopNotifierServiceProvider::class,
LaravelConsoleTaskServiceProvider::class,
LaravelConsoleSummaryServiceProvider::class,
CommandRecorderServiceProvider::class,
];






protected $components = [
Components\Log\Provider::class,
Components\Queue\Provider::class,
Components\Updater\Provider::class,
Components\Database\Provider::class,
Components\ConsoleDusk\Provider::class,
Components\Menu\Provider::class,
Components\Redis\Provider::class,
Components\View\Provider::class,
];




public function bootstrap(Application $app): void
{



$app->make(BaseRegisterProviders::class)
->bootstrap($app);




$this->registerConsoleSchedule($app);




collect($this->providers)
->merge(
collect($this->components)->filter(
function ($component) use ($app) {
return (new $component($app))->isAvailable();
}
)
)
->each(
function ($serviceProviderClass) use ($app) {
$app->register($serviceProviderClass);
}
);
}




public function registerConsoleSchedule(Application $app): void
{
$app->singleton(Schedule::class, function ($app) {
return $app->make(ConsoleKernel::class)->resolveConsoleSchedule();
});
}
}
