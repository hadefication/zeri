<?php

namespace Illuminate\Foundation\Bootstrap;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class RegisterProviders
{





protected static $merge = [];






protected static $bootstrapProviderPath;







public function bootstrap(Application $app)
{
if (! $app->bound('config_loaded_from_cache') ||
$app->make('config_loaded_from_cache') === false) {
$this->mergeAdditionalProviders($app);
}

$app->registerConfiguredProviders();
}






protected function mergeAdditionalProviders(Application $app)
{
if (static::$bootstrapProviderPath &&
file_exists(static::$bootstrapProviderPath)) {
$packageProviders = require static::$bootstrapProviderPath;

foreach ($packageProviders as $index => $provider) {
if (! class_exists($provider)) {
unset($packageProviders[$index]);
}
}
}

$app->make('config')->set(
'app.providers',
array_merge(
$app->make('config')->get('app.providers') ?? ServiceProvider::defaultProviders()->toArray(),
static::$merge,
array_values($packageProviders ?? []),
),
);
}








public static function merge(array $providers, ?string $bootstrapProviderPath = null)
{
static::$bootstrapProviderPath = $bootstrapProviderPath;

static::$merge = array_values(array_filter(array_unique(
array_merge(static::$merge, $providers)
)));
}






public static function flushState()
{
static::$bootstrapProviderPath = null;

static::$merge = [];
}
}
