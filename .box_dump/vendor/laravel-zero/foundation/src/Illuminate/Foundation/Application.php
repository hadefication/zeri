<?php

namespace Illuminate\Foundation;

use Closure;
use Composer\Autoload\ClassLoader;
use Illuminate\Container\Container;
use Illuminate\Contracts\Console\Kernel as ConsoleKernelContract;
use Illuminate\Contracts\Foundation\Application as ApplicationContract;
use Illuminate\Contracts\Foundation\CachesConfiguration;
use Illuminate\Contracts\Foundation\CachesRoutes;
use Illuminate\Contracts\Foundation\MaintenanceMode as MaintenanceModeContract;
use Illuminate\Contracts\Http\Kernel as HttpKernelContract;
use Illuminate\Events\EventServiceProvider;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables;
use Illuminate\Foundation\Events\LocaleUpdated;
use Illuminate\Http\Request;
use Illuminate\Log\Context\ContextServiceProvider;
use Illuminate\Log\LogServiceProvider;
use Illuminate\Routing\RoutingServiceProvider;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Env;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Macroable;
use RuntimeException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use function Illuminate\Filesystem\join_paths;

class Application extends Container implements ApplicationContract, CachesConfiguration, CachesRoutes
{
use Macroable;






const VERSION = '12.17.0';






const MAIN_REQUEST = 1;






protected $basePath;






protected $registeredCallbacks = [];






protected $hasBeenBootstrapped = false;






protected $booted = false;






protected $bootingCallbacks = [];






protected $bootedCallbacks = [];






protected $terminatingCallbacks = [];






protected $serviceProviders = [];






protected $loadedProviders = [];






protected $deferredServices = [];






protected $bootstrapPath;






protected $appPath;






protected $configPath;






protected $databasePath;






protected $langPath;






protected $publicPath;






protected $storagePath;






protected $environmentPath;






protected $environmentFile = '.env';






protected $isRunningInConsole;






protected $namespace;






protected $mergeFrameworkConfiguration = true;






protected $absoluteCachePathPrefixes = ['/', '\\'];






public function __construct($basePath = null)
{
if ($basePath) {
$this->setBasePath($basePath);
}

$this->registerBaseBindings();
$this->registerBaseServiceProviders();
$this->registerCoreContainerAliases();
$this->registerLaravelCloudServices();
}







public static function configure(?string $basePath = null)
{
$basePath = match (true) {
is_string($basePath) => $basePath,
default => static::inferBasePath(),
};

return (new Configuration\ApplicationBuilder(new static($basePath)))
->withKernels()
->withEvents()
->withCommands()
->withProviders();
}






public static function inferBasePath()
{
return match (true) {
isset($_ENV['APP_BASE_PATH']) => $_ENV['APP_BASE_PATH'],
default => dirname(array_values(array_filter(
array_keys(ClassLoader::getRegisteredLoaders()),
fn ($path) => ! str_starts_with($path, 'phar://'),
))[0]),
};
}






public function version()
{
return static::VERSION;
}






protected function registerBaseBindings()
{
static::setInstance($this);

$this->instance('app', $this);

$this->instance(Container::class, $this);
$this->singleton(Mix::class);

$this->singleton(PackageManifest::class, fn () => new PackageManifest(
new Filesystem, $this->basePath(), $this->getCachedPackagesPath()
));
}






protected function registerBaseServiceProviders()
{
$this->register(new EventServiceProvider($this));
$this->register(new LogServiceProvider($this));
$this->register(new ContextServiceProvider($this));
$this->register(new RoutingServiceProvider($this));
}






protected function registerLaravelCloudServices()
{
if (! laravel_cloud()) {
return;
}

$this['events']->listen(
'bootstrapping: *',
fn ($bootstrapper) => Cloud::bootstrapperBootstrapping($this, Str::after($bootstrapper, 'bootstrapping: '))
);

$this['events']->listen(
'bootstrapped: *',
fn ($bootstrapper) => Cloud::bootstrapperBootstrapped($this, Str::after($bootstrapper, 'bootstrapped: '))
);
}







public function bootstrapWith(array $bootstrappers)
{
$this->hasBeenBootstrapped = true;

foreach ($bootstrappers as $bootstrapper) {
$this['events']->dispatch('bootstrapping: '.$bootstrapper, [$this]);

$this->make($bootstrapper)->bootstrap($this);

$this['events']->dispatch('bootstrapped: '.$bootstrapper, [$this]);
}
}







public function afterLoadingEnvironment(Closure $callback)
{
$this->afterBootstrapping(
LoadEnvironmentVariables::class, $callback
);
}








public function beforeBootstrapping($bootstrapper, Closure $callback)
{
$this['events']->listen('bootstrapping: '.$bootstrapper, $callback);
}








public function afterBootstrapping($bootstrapper, Closure $callback)
{
$this['events']->listen('bootstrapped: '.$bootstrapper, $callback);
}






public function hasBeenBootstrapped()
{
return $this->hasBeenBootstrapped;
}







public function setBasePath($basePath)
{
$this->basePath = rtrim($basePath, '\/');

$this->bindPathsInContainer();

return $this;
}






protected function bindPathsInContainer()
{
$this->instance('path', $this->path());
$this->instance('path.base', $this->basePath());
$this->instance('path.config', $this->configPath());
$this->instance('path.database', $this->databasePath());
$this->instance('path.public', $this->publicPath());
$this->instance('path.resources', $this->resourcePath());
$this->instance('path.storage', $this->storagePath());

$this->useBootstrapPath(value(function () {
return is_dir($directory = $this->basePath('.laravel'))
? $directory
: $this->basePath('bootstrap');
}));

$this->useLangPath(value(function () {
return is_dir($directory = $this->resourcePath('lang'))
? $directory
: $this->basePath('lang');
}));
}







public function path($path = '')
{
return $this->joinPaths($this->appPath ?: $this->basePath('app'), $path);
}







public function useAppPath($path)
{
$this->appPath = $path;

$this->instance('path', $path);

return $this;
}







public function basePath($path = '')
{
return $this->joinPaths($this->basePath, $path);
}







public function bootstrapPath($path = '')
{
return $this->joinPaths($this->bootstrapPath, $path);
}






public function getBootstrapProvidersPath()
{
return $this->bootstrapPath('providers.php');
}







public function useBootstrapPath($path)
{
$this->bootstrapPath = $path;

$this->instance('path.bootstrap', $path);

return $this;
}







public function configPath($path = '')
{
return $this->joinPaths($this->configPath ?: $this->basePath('config'), $path);
}







public function useConfigPath($path)
{
$this->configPath = $path;

$this->instance('path.config', $path);

return $this;
}







public function databasePath($path = '')
{
return $this->joinPaths($this->databasePath ?: $this->basePath('database'), $path);
}







public function useDatabasePath($path)
{
$this->databasePath = $path;

$this->instance('path.database', $path);

return $this;
}







public function langPath($path = '')
{
return $this->joinPaths($this->langPath, $path);
}







public function useLangPath($path)
{
$this->langPath = $path;

$this->instance('path.lang', $path);

return $this;
}







public function publicPath($path = '')
{
return $this->joinPaths($this->publicPath ?: $this->basePath('public'), $path);
}







public function usePublicPath($path)
{
$this->publicPath = $path;

$this->instance('path.public', $path);

return $this;
}







public function storagePath($path = '')
{
if (isset($_ENV['LARAVEL_STORAGE_PATH'])) {
return $this->joinPaths($this->storagePath ?: $_ENV['LARAVEL_STORAGE_PATH'], $path);
}

if (isset($_SERVER['LARAVEL_STORAGE_PATH'])) {
return $this->joinPaths($this->storagePath ?: $_SERVER['LARAVEL_STORAGE_PATH'], $path);
}

return $this->joinPaths($this->storagePath ?: $this->basePath('storage'), $path);
}







public function useStoragePath($path)
{
$this->storagePath = $path;

$this->instance('path.storage', $path);

return $this;
}







public function resourcePath($path = '')
{
return $this->joinPaths($this->basePath('resources'), $path);
}









public function viewPath($path = '')
{
$viewPath = rtrim($this['config']->get('view.paths')[0], DIRECTORY_SEPARATOR);

return $this->joinPaths($viewPath, $path);
}








public function joinPaths($basePath, $path = '')
{
return join_paths($basePath, $path);
}






public function environmentPath()
{
return $this->environmentPath ?: $this->basePath;
}







public function useEnvironmentPath($path)
{
$this->environmentPath = $path;

return $this;
}







public function loadEnvironmentFrom($file)
{
$this->environmentFile = $file;

return $this;
}






public function environmentFile()
{
return $this->environmentFile ?: '.env';
}






public function environmentFilePath()
{
return $this->environmentPath().DIRECTORY_SEPARATOR.$this->environmentFile();
}







public function environment(...$environments)
{
if (count($environments) > 0) {
$patterns = is_array($environments[0]) ? $environments[0] : $environments;

return Str::is($patterns, $this['env']);
}

return $this['env'];
}






public function isLocal()
{
return $this['env'] === 'local';
}






public function isProduction()
{
return $this['env'] === 'production';
}







public function detectEnvironment(Closure $callback)
{
$args = $this->runningInConsole() && isset($_SERVER['argv'])
? $_SERVER['argv']
: null;

return $this['env'] = (new EnvironmentDetector)->detect($callback, $args);
}






public function runningInConsole()
{
if ($this->isRunningInConsole === null) {
$this->isRunningInConsole = Env::get('APP_RUNNING_IN_CONSOLE') ?? (\PHP_SAPI === 'cli' || \PHP_SAPI === 'phpdbg');
}

return $this->isRunningInConsole;
}







public function runningConsoleCommand(...$commands)
{
if (! $this->runningInConsole()) {
return false;
}

return in_array(
$_SERVER['argv'][1] ?? null,
is_array($commands[0]) ? $commands[0] : $commands
);
}






public function runningUnitTests()
{
return $this->bound('env') && $this['env'] === 'testing';
}






public function hasDebugModeEnabled()
{
return (bool) $this['config']->get('app.debug');
}







public function registered($callback)
{
$this->registeredCallbacks[] = $callback;
}






public function registerConfiguredProviders()
{
$providers = (new Collection($this->make('config')->get('app.providers')))
->partition(fn ($provider) => str_starts_with($provider, 'Illuminate\\'));

$providers->splice(1, 0, [$this->make(PackageManifest::class)->providers()]);

(new ProviderRepository($this, new Filesystem, $this->getCachedServicesPath()))
->load($providers->collapse()->toArray());

$this->fireAppCallbacks($this->registeredCallbacks);
}








public function register($provider, $force = false)
{
if (($registered = $this->getProvider($provider)) && ! $force) {
return $registered;
}




if (is_string($provider)) {
$provider = $this->resolveProvider($provider);
}

$provider->register();




if (property_exists($provider, 'bindings')) {
foreach ($provider->bindings as $key => $value) {
$this->bind($key, $value);
}
}

if (property_exists($provider, 'singletons')) {
foreach ($provider->singletons as $key => $value) {
$key = is_int($key) ? $value : $key;

$this->singleton($key, $value);
}
}

$this->markAsRegistered($provider);




if ($this->isBooted()) {
$this->bootProvider($provider);
}

return $provider;
}







public function getProvider($provider)
{
$name = is_string($provider) ? $provider : get_class($provider);

return $this->serviceProviders[$name] ?? null;
}







public function getProviders($provider)
{
$name = is_string($provider) ? $provider : get_class($provider);

return Arr::where($this->serviceProviders, fn ($value) => $value instanceof $name);
}







public function resolveProvider($provider)
{
return new $provider($this);
}







protected function markAsRegistered($provider)
{
$class = get_class($provider);

$this->serviceProviders[$class] = $provider;

$this->loadedProviders[$class] = true;
}






public function loadDeferredProviders()
{



foreach ($this->deferredServices as $service => $provider) {
$this->loadDeferredProvider($service);
}

$this->deferredServices = [];
}







public function loadDeferredProvider($service)
{
if (! $this->isDeferredService($service)) {
return;
}

$provider = $this->deferredServices[$service];




if (! isset($this->loadedProviders[$provider])) {
$this->registerDeferredProvider($provider, $service);
}
}








public function registerDeferredProvider($provider, $service = null)
{



if ($service) {
unset($this->deferredServices[$service]);
}

$this->register($instance = new $provider($this));

if (! $this->isBooted()) {
$this->booting(function () use ($instance) {
$this->bootProvider($instance);
});
}
}

/**
@template








*/
public function make($abstract, array $parameters = [])
{
$this->loadDeferredProviderIfNeeded($abstract = $this->getAlias($abstract));

return parent::make($abstract, $parameters);
}

/**
@template










*/
protected function resolve($abstract, $parameters = [], $raiseEvents = true)
{
$this->loadDeferredProviderIfNeeded($abstract = $this->getAlias($abstract));

return parent::resolve($abstract, $parameters, $raiseEvents);
}







protected function loadDeferredProviderIfNeeded($abstract)
{
if ($this->isDeferredService($abstract) && ! isset($this->instances[$abstract])) {
$this->loadDeferredProvider($abstract);
}
}







public function bound($abstract)
{
return $this->isDeferredService($abstract) || parent::bound($abstract);
}






public function isBooted()
{
return $this->booted;
}






public function boot()
{
if ($this->isBooted()) {
return;
}




$this->fireAppCallbacks($this->bootingCallbacks);

array_walk($this->serviceProviders, function ($p) {
$this->bootProvider($p);
});

$this->booted = true;

$this->fireAppCallbacks($this->bootedCallbacks);
}







protected function bootProvider(ServiceProvider $provider)
{
$provider->callBootingCallbacks();

if (method_exists($provider, 'boot')) {
$this->call([$provider, 'boot']);
}

$provider->callBootedCallbacks();
}







public function booting($callback)
{
$this->bootingCallbacks[] = $callback;
}







public function booted($callback)
{
$this->bootedCallbacks[] = $callback;

if ($this->isBooted()) {
$callback($this);
}
}







protected function fireAppCallbacks(array &$callbacks)
{
$index = 0;

while ($index < count($callbacks)) {
$callbacks[$index]($this);

$index++;
}
}






public function handle(SymfonyRequest $request, int $type = self::MAIN_REQUEST, bool $catch = true): SymfonyResponse
{
return $this[HttpKernelContract::class]->handle(Request::createFromBase($request));
}







public function handleRequest(Request $request)
{
$kernel = $this->make(HttpKernelContract::class);

$response = $kernel->handle($request)->send();

$kernel->terminate($request, $response);
}







public function handleCommand(InputInterface $input)
{
$kernel = $this->make(ConsoleKernelContract::class);

$status = $kernel->handle(
$input,
new ConsoleOutput
);

$kernel->terminate($input, $status);

return $status;
}






public function shouldMergeFrameworkConfiguration()
{
return $this->mergeFrameworkConfiguration;
}






public function dontMergeFrameworkConfiguration()
{
$this->mergeFrameworkConfiguration = false;

return $this;
}






public function shouldSkipMiddleware()
{
return $this->bound('middleware.disable') &&
$this->make('middleware.disable') === true;
}






public function getCachedServicesPath()
{
return $this->normalizeCachePath('APP_SERVICES_CACHE', 'cache/services.php');
}






public function getCachedPackagesPath()
{
return $this->normalizeCachePath('APP_PACKAGES_CACHE', 'cache/packages.php');
}






public function configurationIsCached()
{
return is_file($this->getCachedConfigPath());
}






public function getCachedConfigPath()
{
return $this->normalizeCachePath('APP_CONFIG_CACHE', 'cache/config.php');
}






public function routesAreCached()
{
return $this['files']->exists($this->getCachedRoutesPath());
}






public function getCachedRoutesPath()
{
return $this->normalizeCachePath('APP_ROUTES_CACHE', 'cache/routes-v7.php');
}






public function eventsAreCached()
{
return $this['files']->exists($this->getCachedEventsPath());
}






public function getCachedEventsPath()
{
return $this->normalizeCachePath('APP_EVENTS_CACHE', 'cache/events.php');
}








protected function normalizeCachePath($key, $default)
{
if (is_null($env = Env::get($key))) {
return $this->bootstrapPath($default);
}

return Str::startsWith($env, $this->absoluteCachePathPrefixes)
? $env
: $this->basePath($env);
}







public function addAbsoluteCachePathPrefix($prefix)
{
$this->absoluteCachePathPrefixes[] = $prefix;

return $this;
}






public function maintenanceMode()
{
return $this->make(MaintenanceModeContract::class);
}






public function isDownForMaintenance()
{
return $this->maintenanceMode()->active();
}












public function abort($code, $message = '', array $headers = [])
{
if ($code == 404) {
throw new NotFoundHttpException($message, null, 0, $headers);
}

throw new HttpException($code, $message, null, $headers);
}







public function terminating($callback)
{
$this->terminatingCallbacks[] = $callback;

return $this;
}






public function terminate()
{
$index = 0;

while ($index < count($this->terminatingCallbacks)) {
$this->call($this->terminatingCallbacks[$index]);

$index++;
}
}






public function getLoadedProviders()
{
return $this->loadedProviders;
}







public function providerIsLoaded(string $provider)
{
return isset($this->loadedProviders[$provider]);
}






public function getDeferredServices()
{
return $this->deferredServices;
}







public function setDeferredServices(array $services)
{
$this->deferredServices = $services;
}







public function isDeferredService($service)
{
return isset($this->deferredServices[$service]);
}







public function addDeferredServices(array $services)
{
$this->deferredServices = array_merge($this->deferredServices, $services);
}







public function removeDeferredServices(array $services)
{
foreach ($services as $service) {
unset($this->deferredServices[$service]);
}
}







public function provideFacades($namespace)
{
AliasLoader::setFacadeNamespace($namespace);
}






public function getLocale()
{
return $this['config']->get('app.locale');
}






public function currentLocale()
{
return $this->getLocale();
}






public function getFallbackLocale()
{
return $this['config']->get('app.fallback_locale');
}







public function setLocale($locale)
{
$this['config']->set('app.locale', $locale);

$this['translator']->setLocale($locale);

$this['events']->dispatch(new LocaleUpdated($locale));
}







public function setFallbackLocale($fallbackLocale)
{
$this['config']->set('app.fallback_locale', $fallbackLocale);

$this['translator']->setFallback($fallbackLocale);
}







public function isLocale($locale)
{
return $this->getLocale() == $locale;
}






public function registerCoreContainerAliases()
{
foreach ([
'app' => [self::class, \Illuminate\Contracts\Container\Container::class, \Illuminate\Contracts\Foundation\Application::class, \Psr\Container\ContainerInterface::class],
'auth' => [\Illuminate\Auth\AuthManager::class, \Illuminate\Contracts\Auth\Factory::class],
'auth.driver' => [\Illuminate\Contracts\Auth\Guard::class],
'blade.compiler' => [\Illuminate\View\Compilers\BladeCompiler::class],
'cache' => [\Illuminate\Cache\CacheManager::class, \Illuminate\Contracts\Cache\Factory::class],
'cache.store' => [\Illuminate\Cache\Repository::class, \Illuminate\Contracts\Cache\Repository::class, \Psr\SimpleCache\CacheInterface::class],
'cache.psr6' => [\Symfony\Component\Cache\Adapter\Psr16Adapter::class, \Symfony\Component\Cache\Adapter\AdapterInterface::class, \Psr\Cache\CacheItemPoolInterface::class],
'config' => [\Illuminate\Config\Repository::class, \Illuminate\Contracts\Config\Repository::class],
'cookie' => [\Illuminate\Cookie\CookieJar::class, \Illuminate\Contracts\Cookie\Factory::class, \Illuminate\Contracts\Cookie\QueueingFactory::class],
'db' => [\Illuminate\Database\DatabaseManager::class, \Illuminate\Database\ConnectionResolverInterface::class],
'db.connection' => [\Illuminate\Database\Connection::class, \Illuminate\Database\ConnectionInterface::class],
'db.schema' => [\Illuminate\Database\Schema\Builder::class],
'encrypter' => [\Illuminate\Encryption\Encrypter::class, \Illuminate\Contracts\Encryption\Encrypter::class, \Illuminate\Contracts\Encryption\StringEncrypter::class],
'events' => [\Illuminate\Events\Dispatcher::class, \Illuminate\Contracts\Events\Dispatcher::class],
'files' => [\Illuminate\Filesystem\Filesystem::class],
'filesystem' => [\Illuminate\Filesystem\FilesystemManager::class, \Illuminate\Contracts\Filesystem\Factory::class],
'filesystem.disk' => [\Illuminate\Contracts\Filesystem\Filesystem::class],
'filesystem.cloud' => [\Illuminate\Contracts\Filesystem\Cloud::class],
'hash' => [\Illuminate\Hashing\HashManager::class],
'hash.driver' => [\Illuminate\Contracts\Hashing\Hasher::class],
'translator' => [\Illuminate\Translation\Translator::class, \Illuminate\Contracts\Translation\Translator::class],
'log' => [\Illuminate\Log\LogManager::class, \Psr\Log\LoggerInterface::class],
'mail.manager' => [\Illuminate\Mail\MailManager::class, \Illuminate\Contracts\Mail\Factory::class],
'mailer' => [\Illuminate\Mail\Mailer::class, \Illuminate\Contracts\Mail\Mailer::class, \Illuminate\Contracts\Mail\MailQueue::class],
'auth.password' => [\Illuminate\Auth\Passwords\PasswordBrokerManager::class, \Illuminate\Contracts\Auth\PasswordBrokerFactory::class],
'auth.password.broker' => [\Illuminate\Auth\Passwords\PasswordBroker::class, \Illuminate\Contracts\Auth\PasswordBroker::class],
'queue' => [\Illuminate\Queue\QueueManager::class, \Illuminate\Contracts\Queue\Factory::class, \Illuminate\Contracts\Queue\Monitor::class],
'queue.connection' => [\Illuminate\Contracts\Queue\Queue::class],
'queue.failer' => [\Illuminate\Queue\Failed\FailedJobProviderInterface::class],
'redirect' => [\Illuminate\Routing\Redirector::class],
'redis' => [\Illuminate\Redis\RedisManager::class, \Illuminate\Contracts\Redis\Factory::class],
'redis.connection' => [\Illuminate\Redis\Connections\Connection::class, \Illuminate\Contracts\Redis\Connection::class],
'request' => [\Illuminate\Http\Request::class, \Symfony\Component\HttpFoundation\Request::class],
'router' => [\Illuminate\Routing\Router::class, \Illuminate\Contracts\Routing\Registrar::class, \Illuminate\Contracts\Routing\BindingRegistrar::class],
'session' => [\Illuminate\Session\SessionManager::class],
'session.store' => [\Illuminate\Session\Store::class, \Illuminate\Contracts\Session\Session::class],
'url' => [\Illuminate\Routing\UrlGenerator::class, \Illuminate\Contracts\Routing\UrlGenerator::class],
'validator' => [\Illuminate\Validation\Factory::class, \Illuminate\Contracts\Validation\Factory::class],
'view' => [\Illuminate\View\Factory::class, \Illuminate\Contracts\View\Factory::class],
] as $key => $aliases) {
foreach ($aliases as $alias) {
$this->alias($key, $alias);
}
}
}






public function flush()
{
parent::flush();

$this->buildStack = [];
$this->loadedProviders = [];
$this->bootedCallbacks = [];
$this->bootingCallbacks = [];
$this->deferredServices = [];
$this->reboundCallbacks = [];
$this->serviceProviders = [];
$this->resolvingCallbacks = [];
$this->terminatingCallbacks = [];
$this->beforeResolvingCallbacks = [];
$this->afterResolvingCallbacks = [];
$this->globalBeforeResolvingCallbacks = [];
$this->globalResolvingCallbacks = [];
$this->globalAfterResolvingCallbacks = [];
}








public function getNamespace()
{
if (! is_null($this->namespace)) {
return $this->namespace;
}

$composer = json_decode(file_get_contents($this->basePath('composer.json')), true);

foreach ((array) data_get($composer, 'autoload.psr-4') as $namespace => $path) {
foreach ((array) $path as $pathChoice) {
if (realpath($this->path()) === realpath($this->basePath($pathChoice))) {
return $this->namespace = $namespace;
}
}
}

throw new RuntimeException('Unable to detect application namespace.');
}
}
