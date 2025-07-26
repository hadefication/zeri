<?php

declare(strict_types=1);










namespace LaravelZero\Framework\Components\Menu;

use LaravelZero\Framework\Components\AbstractComponentProvider;

use function class_exists;




final class Provider extends AbstractComponentProvider
{



public function isAvailable(): bool
{
return class_exists(\NunoMaduro\LaravelConsoleMenu\LaravelConsoleMenuServiceProvider::class);
}




public function register(): void
{
$this->app->register(\NunoMaduro\LaravelConsoleMenu\LaravelConsoleMenuServiceProvider::class);
}
}
