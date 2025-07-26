<?php

namespace Illuminate\Foundation\Providers;

use Illuminate\Console\Events\CommandFinished;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Contracts\Console\Kernel as ConsoleKernel;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Foundation\MaintenanceMode as MaintenanceModeContract;
use Illuminate\Contracts\View\Factory;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Grammar;
use Illuminate\Foundation\Console\CliDumper;
use Illuminate\Foundation\Exceptions\Renderer\Listener;
use Illuminate\Foundation\Exceptions\Renderer\Mappers\BladeMapper;
use Illuminate\Foundation\Exceptions\Renderer\Renderer;
use Illuminate\Foundation\Http\HtmlDumper;
use Illuminate\Foundation\MaintenanceModeManager;
use Illuminate\Foundation\Precognition;
use Illuminate\Foundation\Vite;
use Illuminate\Http\Client\Factory as HttpFactory;
use Illuminate\Http\Request;
use Illuminate\Log\Events\MessageLogged;
use Illuminate\Queue\Events\JobAttempted;
use Illuminate\Support\AggregateServiceProvider;
use Illuminate\Support\Defer\DeferredCallbackCollection;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Uri;
use Illuminate\Testing\LoggedExceptionCollection;
use Illuminate\Testing\ParallelTestingServiceProvider;
use Illuminate\Validation\ValidationException;
use Symfony\Component\ErrorHandler\ErrorRenderer\HtmlErrorRenderer;
use Symfony\Component\VarDumper\Caster\StubCaster;
use Symfony\Component\VarDumper\Cloner\AbstractCloner;

class FoundationServiceProvider extends AggregateServiceProvider
{





protected $providers = [
FormRequestServiceProvider::class,
ParallelTestingServiceProvider::class,
];






public $singletons = [
HttpFactory::class => HttpFactory::class,
Vite::class => Vite::class,
];






public function boot()
{
if ($this->app->runningInConsole()) {
$this->publishes([
__DIR__.'/../Exceptions/views' => $this->app->resourcePath('views/errors/'),
], 'laravel-errors');
}

if ($this->app->hasDebugModeEnabled()) {
$this->app->make(Listener::class)->registerListeners(
$this->app->make(Dispatcher::class)
);
}
}






public function register()
{
parent::register();

$this->registerConsoleSchedule();
$this->registerDumper();
$this->registerRequestValidation();
$this->registerRequestSignatureValidation();
$this->registerUriUrlGeneration();
$this->registerDeferHandler();
$this->registerExceptionTracking();
$this->registerExceptionRenderer();
$this->registerMaintenanceModeManager();
}






public function registerConsoleSchedule()
{
$this->app->singleton(Schedule::class, function ($app) {
return $app->make(ConsoleKernel::class)->resolveConsoleSchedule();
});
}






public function registerDumper()
{
AbstractCloner::$defaultCasters[ConnectionInterface::class] ??= [StubCaster::class, 'cutInternals'];
AbstractCloner::$defaultCasters[Container::class] ??= [StubCaster::class, 'cutInternals'];
AbstractCloner::$defaultCasters[Dispatcher::class] ??= [StubCaster::class, 'cutInternals'];
AbstractCloner::$defaultCasters[Factory::class] ??= [StubCaster::class, 'cutInternals'];
AbstractCloner::$defaultCasters[Grammar::class] ??= [StubCaster::class, 'cutInternals'];

$basePath = $this->app->basePath();

$compiledViewPath = $this->app['config']->get('view.compiled');

$format = $_SERVER['VAR_DUMPER_FORMAT'] ?? null;

match (true) {
'html' == $format => HtmlDumper::register($basePath, $compiledViewPath),
'cli' == $format => CliDumper::register($basePath, $compiledViewPath),
'server' == $format => null,
$format && 'tcp' == parse_url($format, PHP_URL_SCHEME) => null,
default => in_array(PHP_SAPI, ['cli', 'phpdbg']) ? CliDumper::register($basePath, $compiledViewPath) : HtmlDumper::register($basePath, $compiledViewPath),
};
}






public function registerRequestValidation()
{
Request::macro('validate', function (array $rules, ...$params) {
return tap(validator($this->all(), $rules, ...$params), function ($validator) {
if ($this->isPrecognitive()) {
$validator->after(Precognition::afterValidationHook($this))
->setRules(
$this->filterPrecognitiveRules($validator->getRulesWithoutPlaceholders())
);
}
})->validate();
});

Request::macro('validateWithBag', function (string $errorBag, array $rules, ...$params) {
try {
return $this->validate($rules, ...$params);
} catch (ValidationException $e) {
$e->errorBag = $errorBag;

throw $e;
}
});
}






public function registerRequestSignatureValidation()
{
Request::macro('hasValidSignature', function ($absolute = true) {
return URL::hasValidSignature($this, $absolute);
});

Request::macro('hasValidRelativeSignature', function () {
return URL::hasValidSignature($this, $absolute = false);
});

Request::macro('hasValidSignatureWhileIgnoring', function ($ignoreQuery = [], $absolute = true) {
return URL::hasValidSignature($this, $absolute, $ignoreQuery);
});

Request::macro('hasValidRelativeSignatureWhileIgnoring', function ($ignoreQuery = []) {
return URL::hasValidSignature($this, $absolute = false, $ignoreQuery);
});
}






protected function registerUriUrlGeneration()
{
Uri::setUrlGeneratorResolver(fn () => app('url'));
}






protected function registerDeferHandler()
{
$this->app->scoped(DeferredCallbackCollection::class);

$this->app['events']->listen(function (CommandFinished $event) {
app(DeferredCallbackCollection::class)->invokeWhen(fn ($callback) => app()->runningInConsole() && ($event->exitCode === 0 || $callback->always)
);
});

$this->app['events']->listen(function (JobAttempted $event) {
app(DeferredCallbackCollection::class)->invokeWhen(fn ($callback) => $event->connectionName !== 'sync' && ($event->successful() || $callback->always)
);
});
}






protected function registerExceptionTracking()
{
if (! $this->app->runningUnitTests()) {
return;
}

$this->app->instance(
LoggedExceptionCollection::class,
new LoggedExceptionCollection
);

$this->app->make('events')->listen(MessageLogged::class, function ($event) {
if (isset($event->context['exception'])) {
$this->app->make(LoggedExceptionCollection::class)
->push($event->context['exception']);
}
});
}






protected function registerExceptionRenderer()
{
$this->loadViewsFrom(__DIR__.'/../Exceptions/views', 'laravel-exceptions');

if (! $this->app->hasDebugModeEnabled()) {
return;
}

$this->loadViewsFrom(__DIR__.'/../resources/exceptions/renderer', 'laravel-exceptions-renderer');

$this->app->singleton(Renderer::class, function (Application $app) {
$errorRenderer = new HtmlErrorRenderer(
$app['config']->get('app.debug'),
);

return new Renderer(
$app->make(Factory::class),
$app->make(Listener::class),
$errorRenderer,
$app->make(BladeMapper::class),
$app->basePath(),
);
});

$this->app->singleton(Listener::class);
}






public function registerMaintenanceModeManager()
{
$this->app->singleton(MaintenanceModeManager::class);

$this->app->bind(
MaintenanceModeContract::class,
fn () => $this->app->make(MaintenanceModeManager::class)->driver()
);
}
}
