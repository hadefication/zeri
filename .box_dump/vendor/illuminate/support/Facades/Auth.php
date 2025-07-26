<?php

namespace Illuminate\Support\Facades;

use Laravel\Ui\UiServiceProvider;
use RuntimeException;































































class Auth extends Facade
{





protected static function getFacadeAccessor()
{
return 'auth';
}









public static function routes(array $options = [])
{
if (! static::$app->providerIsLoaded(UiServiceProvider::class)) {
throw new RuntimeException('In order to use the Auth::routes() method, please install the laravel/ui package.');
}

static::$app->make('router')->auth($options);
}
}
