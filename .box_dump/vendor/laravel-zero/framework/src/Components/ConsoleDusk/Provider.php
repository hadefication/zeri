<?php

declare(strict_types=1);










namespace LaravelZero\Framework\Components\ConsoleDusk;

use LaravelZero\Framework\Components\AbstractComponentProvider;

use function class_exists;




final class Provider extends AbstractComponentProvider
{



public function isAvailable(): bool
{
return class_exists(\NunoMaduro\LaravelConsoleDusk\LaravelConsoleDuskServiceProvider::class);
}




public function register(): void
{
$this->app->register(\NunoMaduro\LaravelConsoleDusk\LaravelConsoleDuskServiceProvider::class);
}
}
