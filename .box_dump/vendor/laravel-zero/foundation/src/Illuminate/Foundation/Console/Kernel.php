<?php

namespace Illuminate\Foundation\Console;

use Carbon\CarbonInterval;
use Closure;
use DateTimeInterface;
use Illuminate\Console\Application as Artisan;
use Illuminate\Console\Command;
use Illuminate\Console\Events\CommandFinished;
use Illuminate\Console\Events\CommandStarting;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Contracts\Console\Kernel as KernelContract;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Events\Terminating;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Env;
use Illuminate\Support\InteractsWithTime;
use Illuminate\Support\Str;
use ReflectionClass;
use SplFileInfo;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Finder\Finder;
use Throwable;

class Kernel implements KernelContract
{
use InteractsWithTime;






protected $app;






protected $events;






protected $symfonyDispatcher;






protected $artisan;






protected $commands = [];






protected $commandPaths = [];






protected $commandRoutePaths = [];






protected $commandsLoaded = false;






protected $loadedPaths = [];






protected $commandLifecycleDurationHandlers = [];






protected $commandStartedAt;






protected $bootstrappers = [
\Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables::class,
\Illuminate\Foundation\Bootstrap\LoadConfiguration::class,
\Illuminate\Foundation\Bootstrap\HandleExceptions::class,
\Illuminate\Foundation\Bootstrap\RegisterFacades::class,
\Illuminate\Foundation\Bootstrap\SetRequestForConsole::class,
\Illuminate\Foundation\Bootstrap\RegisterProviders::class,
\Illuminate\Foundation\Bootstrap\BootProviders::class,
];







public function __construct(Application $app, Dispatcher $events)
{
if (! defined('ARTISAN_BINARY')) {
define('ARTISAN_BINARY', 'artisan');
}

$this->app = $app;
$this->events = $events;

$this->app->booted(function () {
if (! $this->app->runningUnitTests()) {
$this->rerouteSymfonyCommandEvents();
}
});
}








public function rerouteSymfonyCommandEvents()
{
if (is_null($this->symfonyDispatcher)) {
$this->symfonyDispatcher = new EventDispatcher;

$this->symfonyDispatcher->addListener(ConsoleEvents::COMMAND, function (ConsoleCommandEvent $event) {
$this->events->dispatch(
new CommandStarting($event->getCommand()?->getName() ?? '', $event->getInput(), $event->getOutput())
);
});

$this->symfonyDispatcher->addListener(ConsoleEvents::TERMINATE, function (ConsoleTerminateEvent $event) {
$this->events->dispatch(
new CommandFinished($event->getCommand()?->getName() ?? '', $event->getInput(), $event->getOutput(), $event->getExitCode())
);
});
}

return $this;
}








public function handle($input, $output = null)
{
$this->commandStartedAt = Carbon::now();

try {
if (in_array($input->getFirstArgument(), ['env:encrypt', 'env:decrypt'], true)) {
$this->bootstrapWithoutBootingProviders();
}

$this->bootstrap();

return $this->getArtisan()->run($input, $output);
} catch (Throwable $e) {
$this->reportException($e);

$this->renderException($output, $e);

return 1;
}
}








public function terminate($input, $status)
{
$this->events->dispatch(new Terminating);

$this->app->terminate();

if ($this->commandStartedAt === null) {
return;
}

$this->commandStartedAt->setTimezone($this->app['config']->get('app.timezone') ?? 'UTC');

foreach ($this->commandLifecycleDurationHandlers as ['threshold' => $threshold, 'handler' => $handler]) {
$end ??= Carbon::now();

if ($this->commandStartedAt->diffInMilliseconds($end) > $threshold) {
$handler($this->commandStartedAt, $input, $status);
}
}

$this->commandStartedAt = null;
}








public function whenCommandLifecycleIsLongerThan($threshold, $handler)
{
$threshold = $threshold instanceof DateTimeInterface
? $this->secondsUntil($threshold) * 1000
: $threshold;

$threshold = $threshold instanceof CarbonInterval
? $threshold->totalMilliseconds
: $threshold;

$this->commandLifecycleDurationHandlers[] = [
'threshold' => $threshold,
'handler' => $handler,
];
}






public function commandStartedAt()
{
return $this->commandStartedAt;
}







protected function schedule(Schedule $schedule)
{

}






public function resolveConsoleSchedule()
{
return tap(new Schedule($this->scheduleTimezone()), function ($schedule) {
$this->schedule($schedule->useCache($this->scheduleCache()));
});
}






protected function scheduleTimezone()
{
$config = $this->app['config'];

return $config->get('app.schedule_timezone', $config->get('app.timezone'));
}






protected function scheduleCache()
{
return $this->app['config']->get('cache.schedule_store', Env::get('SCHEDULE_CACHE_DRIVER', function () {
return Env::get('SCHEDULE_CACHE_STORE');
}));
}






protected function commands()
{

}








public function command($signature, Closure $callback)
{
$command = new ClosureCommand($signature, $callback);

Artisan::starting(function ($artisan) use ($command) {
$artisan->add($command);
});

return $command;
}







protected function load($paths)
{
$paths = array_unique(Arr::wrap($paths));

$paths = array_filter($paths, function ($path) {
return is_dir($path);
});

if (empty($paths)) {
return;
}

$this->loadedPaths = array_values(
array_unique(array_merge($this->loadedPaths, $paths))
);

$namespace = $this->app->getNamespace();

foreach (Finder::create()->in($paths)->files() as $file) {
$command = $this->commandClassFromFile($file, $namespace);

if (is_subclass_of($command, Command::class) &&
! (new ReflectionClass($command))->isAbstract()) {
Artisan::starting(function ($artisan) use ($command) {
$artisan->resolve($command);
});
}
}
}








protected function commandClassFromFile(SplFileInfo $file, string $namespace): string
{
return $namespace.str_replace(
['/', '.php'],
['\\', ''],
Str::after($file->getPathname(), app_path().DIRECTORY_SEPARATOR)
);
}







public function registerCommand($command)
{
$this->getArtisan()->add($command);
}











public function call($command, array $parameters = [], $outputBuffer = null)
{
if (in_array($command, ['env:encrypt', 'env:decrypt'], true)) {
$this->bootstrapWithoutBootingProviders();
}

$this->bootstrap();

return $this->getArtisan()->call($command, $parameters, $outputBuffer);
}








public function queue($command, array $parameters = [])
{
return QueuedCommand::dispatch(func_get_args());
}






public function all()
{
$this->bootstrap();

return $this->getArtisan()->all();
}






public function output()
{
$this->bootstrap();

return $this->getArtisan()->output();
}






public function bootstrap()
{
if (! $this->app->hasBeenBootstrapped()) {
$this->app->bootstrapWith($this->bootstrappers());
}

$this->app->loadDeferredProviders();

if (! $this->commandsLoaded) {
$this->commands();

if ($this->shouldDiscoverCommands()) {
$this->discoverCommands();
}

$this->commandsLoaded = true;
}
}






protected function discoverCommands()
{
foreach ($this->commandPaths as $path) {
$this->load($path);
}

foreach ($this->commandRoutePaths as $path) {
if (file_exists($path)) {
require $path;
}
}
}






public function bootstrapWithoutBootingProviders()
{
$this->app->bootstrapWith(
(new Collection($this->bootstrappers()))
->reject(fn ($bootstrapper) => $bootstrapper === \Illuminate\Foundation\Bootstrap\BootProviders::class)
->all()
);
}






protected function shouldDiscoverCommands()
{
return get_class($this) === __CLASS__;
}






protected function getArtisan()
{
if (is_null($this->artisan)) {
$this->artisan = (new Artisan($this->app, $this->events, $this->app->version()))
->resolveCommands($this->commands)
->setContainerCommandLoader();

if ($this->symfonyDispatcher instanceof EventDispatcher) {
$this->artisan->setDispatcher($this->symfonyDispatcher);
$this->artisan->setSignalsToDispatchEvent();
}
}

return $this->artisan;
}







public function setArtisan($artisan)
{
$this->artisan = $artisan;
}







public function addCommands(array $commands)
{
$this->commands = array_values(array_unique(array_merge($this->commands, $commands)));

return $this;
}







public function addCommandPaths(array $paths)
{
$this->commandPaths = array_values(array_unique(array_merge($this->commandPaths, $paths)));

return $this;
}







public function addCommandRoutePaths(array $paths)
{
$this->commandRoutePaths = array_values(array_unique(array_merge($this->commandRoutePaths, $paths)));

return $this;
}






protected function bootstrappers()
{
return $this->bootstrappers;
}







protected function reportException(Throwable $e)
{
$this->app[ExceptionHandler::class]->report($e);
}








protected function renderException($output, Throwable $e)
{
$this->app[ExceptionHandler::class]->renderForConsole($output, $e);
}
}
