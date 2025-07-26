<?php

namespace Illuminate\Foundation\Support\Providers;

use Closure;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Traits\ForwardsCalls;

/**
@mixin
*/
class RouteServiceProvider extends ServiceProvider
{
use ForwardsCalls;






protected $namespace;






protected $loadRoutesUsing;






protected static $alwaysLoadRoutesUsing;






protected static $alwaysLoadCachedRoutesUsing;






public function register()
{
$this->booted(function () {
$this->setRootControllerNamespace();

if ($this->routesAreCached()) {
$this->loadCachedRoutes();
} else {
$this->loadRoutes();

$this->app->booted(function () {
$this->app['router']->getRoutes()->refreshNameLookups();
$this->app['router']->getRoutes()->refreshActionLookups();
});
}
});
}






public function boot()
{

}







protected function routes(Closure $routesCallback)
{
$this->loadRoutesUsing = $routesCallback;

return $this;
}







public static function loadRoutesUsing(?Closure $routesCallback)
{
self::$alwaysLoadRoutesUsing = $routesCallback;
}







public static function loadCachedRoutesUsing(?Closure $routesCallback)
{
self::$alwaysLoadCachedRoutesUsing = $routesCallback;
}






protected function setRootControllerNamespace()
{
if (! is_null($this->namespace)) {
$this->app[UrlGenerator::class]->setRootControllerNamespace($this->namespace);
}
}






protected function routesAreCached()
{
return $this->app->routesAreCached();
}






protected function loadCachedRoutes()
{
if (! is_null(self::$alwaysLoadCachedRoutesUsing)) {
$this->app->call(self::$alwaysLoadCachedRoutesUsing);

return;
}

$this->app->booted(function () {
require $this->app->getCachedRoutesPath();
});
}






protected function loadRoutes()
{
if (! is_null(self::$alwaysLoadRoutesUsing)) {
$this->app->call(self::$alwaysLoadRoutesUsing);
}

if (! is_null($this->loadRoutesUsing)) {
$this->app->call($this->loadRoutesUsing);
} elseif (method_exists($this, 'map')) {
$this->app->call([$this, 'map']);
}
}








public function __call($method, $parameters)
{
return $this->forwardCallTo(
$this->app->make(Router::class), $method, $parameters
);
}
}
