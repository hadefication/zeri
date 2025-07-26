<?php

namespace Illuminate\Support;

use Closure;
use Illuminate\Console\Application as Artisan;
use Illuminate\Contracts\Foundation\CachesConfiguration;
use Illuminate\Contracts\Foundation\CachesRoutes;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Database\Eloquent\Factory as ModelFactory;
use Illuminate\View\Compilers\BladeCompiler;





abstract class ServiceProvider
{





protected $app;






protected $bootingCallbacks = [];






protected $bootedCallbacks = [];






public static $publishes = [];






public static $publishGroups = [];






protected static $publishableMigrationPaths = [];






public static array $optimizeCommands = [];






public static array $optimizeClearCommands = [];






public function __construct($app)
{
$this->app = $app;
}






public function register()
{

}







public function booting(Closure $callback)
{
$this->bootingCallbacks[] = $callback;
}







public function booted(Closure $callback)
{
$this->bootedCallbacks[] = $callback;
}






public function callBootingCallbacks()
{
$index = 0;

while ($index < count($this->bootingCallbacks)) {
$this->app->call($this->bootingCallbacks[$index]);

$index++;
}
}






public function callBootedCallbacks()
{
$index = 0;

while ($index < count($this->bootedCallbacks)) {
$this->app->call($this->bootedCallbacks[$index]);

$index++;
}
}








protected function mergeConfigFrom($path, $key)
{
if (! ($this->app instanceof CachesConfiguration && $this->app->configurationIsCached())) {
$config = $this->app->make('config');

$config->set($key, array_merge(
require $path, $config->get($key, [])
));
}
}








protected function replaceConfigRecursivelyFrom($path, $key)
{
if (! ($this->app instanceof CachesConfiguration && $this->app->configurationIsCached())) {
$config = $this->app->make('config');

$config->set($key, array_replace_recursive(
require $path, $config->get($key, [])
));
}
}







protected function loadRoutesFrom($path)
{
if (! ($this->app instanceof CachesRoutes && $this->app->routesAreCached())) {
require $path;
}
}








protected function loadViewsFrom($path, $namespace)
{
$this->callAfterResolving('view', function ($view) use ($path, $namespace) {
if (isset($this->app->config['view']['paths']) &&
is_array($this->app->config['view']['paths'])) {
foreach ($this->app->config['view']['paths'] as $viewPath) {
if (is_dir($appPath = $viewPath.'/vendor/'.$namespace)) {
$view->addNamespace($namespace, $appPath);
}
}
}

$view->addNamespace($namespace, $path);
});
}








protected function loadViewComponentsAs($prefix, array $components)
{
$this->callAfterResolving(BladeCompiler::class, function ($blade) use ($prefix, $components) {
foreach ($components as $alias => $component) {
$blade->component($component, is_string($alias) ? $alias : null, $prefix);
}
});
}








protected function loadTranslationsFrom($path, $namespace = null)
{
$this->callAfterResolving('translator', fn ($translator) => is_null($namespace)
? $translator->addPath($path)
: $translator->addNamespace($namespace, $path));
}







protected function loadJsonTranslationsFrom($path)
{
$this->callAfterResolving('translator', function ($translator) use ($path) {
$translator->addJsonPath($path);
});
}







protected function loadMigrationsFrom($paths)
{
$this->callAfterResolving('migrator', function ($migrator) use ($paths) {
foreach ((array) $paths as $path) {
$migrator->path($path);
}
});
}









protected function loadFactoriesFrom($paths)
{
$this->callAfterResolving(ModelFactory::class, function ($factory) use ($paths) {
foreach ((array) $paths as $path) {
$factory->load($path);
}
});
}








protected function callAfterResolving($name, $callback)
{
$this->app->afterResolving($name, $callback);

if ($this->app->resolved($name)) {
$callback($this->app->make($name), $this->app);
}
}








protected function publishesMigrations(array $paths, $groups = null)
{
$this->publishes($paths, $groups);

if ($this->app->config->get('database.migrations.update_date_on_publish', false)) {
static::$publishableMigrationPaths = array_unique(array_merge(static::$publishableMigrationPaths, array_keys($paths)));
}
}








protected function publishes(array $paths, $groups = null)
{
$this->ensurePublishArrayInitialized($class = static::class);

static::$publishes[$class] = array_merge(static::$publishes[$class], $paths);

foreach ((array) $groups as $group) {
$this->addPublishGroup($group, $paths);
}
}







protected function ensurePublishArrayInitialized($class)
{
if (! array_key_exists($class, static::$publishes)) {
static::$publishes[$class] = [];
}
}








protected function addPublishGroup($group, $paths)
{
if (! array_key_exists($group, static::$publishGroups)) {
static::$publishGroups[$group] = [];
}

static::$publishGroups[$group] = array_merge(
static::$publishGroups[$group], $paths
);
}








public static function pathsToPublish($provider = null, $group = null)
{
if (! is_null($paths = static::pathsForProviderOrGroup($provider, $group))) {
return $paths;
}

return (new Collection(static::$publishes))->reduce(function ($paths, $p) {
return array_merge($paths, $p);
}, []);
}








protected static function pathsForProviderOrGroup($provider, $group)
{
if ($provider && $group) {
return static::pathsForProviderAndGroup($provider, $group);
} elseif ($group && array_key_exists($group, static::$publishGroups)) {
return static::$publishGroups[$group];
} elseif ($provider && array_key_exists($provider, static::$publishes)) {
return static::$publishes[$provider];
} elseif ($group || $provider) {
return [];
}
}








protected static function pathsForProviderAndGroup($provider, $group)
{
if (! empty(static::$publishes[$provider]) && ! empty(static::$publishGroups[$group])) {
return array_intersect_key(static::$publishes[$provider], static::$publishGroups[$group]);
}

return [];
}






public static function publishableProviders()
{
return array_keys(static::$publishes);
}






public static function publishableMigrationPaths()
{
return static::$publishableMigrationPaths;
}






public static function publishableGroups()
{
return array_keys(static::$publishGroups);
}







public function commands($commands)
{
$commands = is_array($commands) ? $commands : func_get_args();

Artisan::starting(function ($artisan) use ($commands) {
$artisan->resolveCommands($commands);
});
}









protected function optimizes(?string $optimize = null, ?string $clear = null, ?string $key = null)
{
$key ??= (string) Str::of(get_class($this))
->classBasename()
->before('ServiceProvider')
->kebab()
->lower()
->trim();

if (empty($key)) {
$key = class_basename(get_class($this));
}

if ($optimize) {
static::$optimizeCommands[$key] = $optimize;
}

if ($clear) {
static::$optimizeClearCommands[$key] = $clear;
}
}






public function provides()
{
return [];
}






public function when()
{
return [];
}






public function isDeferred()
{
return $this instanceof DeferrableProvider;
}






public static function defaultProviders()
{
return new DefaultProviders;
}








public static function addProviderToBootstrapFile(string $provider, ?string $path = null)
{
$path ??= app()->getBootstrapProvidersPath();

if (! file_exists($path)) {
return false;
}

if (function_exists('opcache_invalidate')) {
opcache_invalidate($path, true);
}

$providers = (new Collection(require $path))
->merge([$provider])
->unique()
->sort()
->values()
->map(fn ($p) => '    '.$p.'::class,')
->implode(PHP_EOL);

$content = '<?php

return [
'.$providers.'
];';

file_put_contents($path, $content.PHP_EOL);

return true;
}
}
