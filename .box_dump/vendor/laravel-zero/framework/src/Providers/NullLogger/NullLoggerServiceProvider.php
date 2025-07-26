<?php

declare(strict_types=1);










namespace LaravelZero\Framework\Providers\NullLogger;

use Illuminate\Support\ServiceProvider;
use Psr\Log\NullLogger;




final class NullLoggerServiceProvider extends ServiceProvider
{



public function register(): void
{
$this->app->singleton('log', NullLogger::class);
}
}
