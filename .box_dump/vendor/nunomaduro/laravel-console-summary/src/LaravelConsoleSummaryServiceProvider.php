<?php

declare(strict_types=1);










namespace NunoMaduro\LaravelConsoleSummary;

use Illuminate\Support\ServiceProvider;
use NunoMaduro\LaravelConsoleSummary\Contracts\DescriberContract;

class LaravelConsoleSummaryServiceProvider extends ServiceProvider
{
public function boot(): void
{
$this->publishes([
__DIR__.'/../config/config.php' => config_path('laravel-console-summary.php'),
], 'laravel-console-summary-config');

$this->commands(SummaryCommand::class);
}


public function register(): void
{
$this->mergeConfigFrom(__DIR__.'/../config/config.php', 'laravel-console-summary');
$this->app->singleton(DescriberContract::class, Describer::class);
}
}
