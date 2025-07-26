<?php

namespace Illuminate\View;

use Illuminate\Container\Container;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\Compilers\BladeCompiler;
use Illuminate\View\Engines\CompilerEngine;
use Illuminate\View\Engines\EngineResolver;
use Illuminate\View\Engines\FileEngine;
use Illuminate\View\Engines\PhpEngine;

class ViewServiceProvider extends ServiceProvider
{





public function register()
{
$this->registerFactory();
$this->registerViewFinder();
$this->registerBladeCompiler();
$this->registerEngineResolver();

$this->app->terminating(static function () {
Component::flushCache();
});
}






public function registerFactory()
{
$this->app->singleton('view', function ($app) {



$resolver = $app['view.engine.resolver'];

$finder = $app['view.finder'];

$factory = $this->createFactory($resolver, $finder, $app['events']);




$factory->setContainer($app);

$factory->share('app', $app);

$app->terminating(static function () {
Component::forgetFactory();
});

return $factory;
});
}









protected function createFactory($resolver, $finder, $events)
{
return new Factory($resolver, $finder, $events);
}






public function registerViewFinder()
{
$this->app->bind('view.finder', function ($app) {
return new FileViewFinder($app['files'], $app['config']['view.paths']);
});
}






public function registerBladeCompiler()
{
$this->app->singleton('blade.compiler', function ($app) {
return tap(new BladeCompiler(
$app['files'],
$app['config']['view.compiled'],
$app['config']->get('view.relative_hash', false) ? $app->basePath() : '',
$app['config']->get('view.cache', true),
$app['config']->get('view.compiled_extension', 'php'),
$app['config']->get('view.check_cache_timestamps', true),
), function ($blade) {
$blade->component('dynamic-component', DynamicComponent::class);
});
});
}






public function registerEngineResolver()
{
$this->app->singleton('view.engine.resolver', function () {
$resolver = new EngineResolver;




foreach (['file', 'php', 'blade'] as $engine) {
$this->{'register'.ucfirst($engine).'Engine'}($resolver);
}

return $resolver;
});
}







public function registerFileEngine($resolver)
{
$resolver->register('file', function () {
return new FileEngine(Container::getInstance()->make('files'));
});
}







public function registerPhpEngine($resolver)
{
$resolver->register('php', function () {
return new PhpEngine(Container::getInstance()->make('files'));
});
}







public function registerBladeEngine($resolver)
{
$resolver->register('blade', function () {
$app = Container::getInstance();

$compiler = new CompilerEngine(
$app->make('blade.compiler'),
$app->make('files'),
);

$app->terminating(static function () use ($compiler) {
$compiler->forgetCompiledOrNotExpired();
});

return $compiler;
});
}
}
