<?php

declare(strict_types=1);










namespace LaravelZero\Framework\Providers\CommandRecorder;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;




final class CommandRecorderServiceProvider extends BaseServiceProvider
{



public function register(): void
{
$this->app->singleton(CommandRecorderRepository::class, function () {
return new CommandRecorderRepository;
});
}
}
