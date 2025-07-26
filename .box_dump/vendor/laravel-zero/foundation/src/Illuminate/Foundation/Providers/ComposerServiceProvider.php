<?php

namespace Illuminate\Foundation\Providers;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\Composer;
use Illuminate\Support\ServiceProvider;

class ComposerServiceProvider extends ServiceProvider implements DeferrableProvider
{





public function register()
{
$this->app->singleton('composer', function ($app) {
return new Composer($app['files'], $app->basePath());
});
}






public function provides()
{
return ['composer'];
}
}
