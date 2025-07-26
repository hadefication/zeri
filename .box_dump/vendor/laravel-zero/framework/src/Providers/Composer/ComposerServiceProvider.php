<?php

declare(strict_types=1);










namespace LaravelZero\Framework\Providers\Composer;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use LaravelZero\Framework\Contracts\Providers\ComposerContract;




final class ComposerServiceProvider extends BaseServiceProvider
{



public function register(): void
{
$this->app->singleton(
ComposerContract::class,
function ($app) {
return new Composer($app);
}
);
}
}
