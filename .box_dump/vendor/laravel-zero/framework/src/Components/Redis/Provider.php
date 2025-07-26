<?php

declare(strict_types=1);










namespace LaravelZero\Framework\Components\Redis;

use Illuminate\Redis\RedisServiceProvider;
use LaravelZero\Framework\Components\AbstractComponentProvider;

use function class_exists;


final class Provider extends AbstractComponentProvider
{

public function isAvailable(): bool
{
return class_exists(RedisServiceProvider::class);
}


public function register(): void
{
$this->app->register(RedisServiceProvider::class);
}
}
