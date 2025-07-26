<?php

namespace Illuminate\Filesystem;

use Illuminate\Contracts\Foundation\CachesRoutes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class FilesystemServiceProvider extends ServiceProvider
{





public function boot()
{
$this->serveFiles();
}






public function register()
{
$this->registerNativeFilesystem();
$this->registerFlysystem();
}






protected function registerNativeFilesystem()
{
$this->app->singleton('files', function () {
return new Filesystem;
});
}






protected function registerFlysystem()
{
$this->registerManager();

$this->app->singleton('filesystem.disk', function ($app) {
return $app['filesystem']->disk($this->getDefaultDriver());
});

$this->app->singleton('filesystem.cloud', function ($app) {
return $app['filesystem']->disk($this->getCloudDriver());
});
}






protected function registerManager()
{
$this->app->singleton('filesystem', function ($app) {
return new FilesystemManager($app);
});
}






protected function serveFiles()
{
if ($this->app instanceof CachesRoutes && $this->app->routesAreCached()) {
return;
}

foreach ($this->app['config']['filesystems.disks'] ?? [] as $disk => $config) {
if (! $this->shouldServeFiles($config)) {
continue;
}

$this->app->booted(function ($app) use ($disk, $config) {
$uri = isset($config['url'])
? rtrim(parse_url($config['url'])['path'], '/')
: '/storage';

$isProduction = $app->isProduction();

Route::get($uri.'/{path}', function (Request $request, string $path) use ($disk, $config, $isProduction) {
return (new ServeFile(
$disk,
$config,
$isProduction
))($request, $path);
})->where('path', '.*')->name('storage.'.$disk);
});
}
}







protected function shouldServeFiles(array $config)
{
return $config['driver'] === 'local' && ($config['serve'] ?? false);
}






protected function getDefaultDriver()
{
return $this->app['config']['filesystems.default'];
}






protected function getCloudDriver()
{
return $this->app['config']['filesystems.cloud'];
}
}
