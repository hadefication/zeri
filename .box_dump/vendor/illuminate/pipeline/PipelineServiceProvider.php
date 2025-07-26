<?php

namespace Illuminate\Pipeline;

use Illuminate\Contracts\Pipeline\Hub as PipelineHubContract;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class PipelineServiceProvider extends ServiceProvider implements DeferrableProvider
{





public function register()
{
$this->app->singleton(
PipelineHubContract::class,
Hub::class
);

$this->app->bind('pipeline', fn ($app) => new Pipeline($app));
}






public function provides()
{
return [
PipelineHubContract::class,
'pipeline',
];
}
}
