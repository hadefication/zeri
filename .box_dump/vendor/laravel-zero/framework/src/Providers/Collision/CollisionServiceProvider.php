<?php

declare(strict_types=1);










namespace LaravelZero\Framework\Providers\Collision;

use NunoMaduro\Collision\Adapters\Laravel\CollisionServiceProvider as BaseCollisionServiceProvider;




final class CollisionServiceProvider extends BaseCollisionServiceProvider
{



public function register(): void
{
if (! $this->app->environment('production')) {
$this->app->register(BaseCollisionServiceProvider::class);
}
}
}
