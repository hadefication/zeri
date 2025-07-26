<?php

namespace Illuminate\Foundation\Support\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Events\DiscoverEvents;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\LazyCollection;
use Illuminate\Support\ServiceProvider;

class EventServiceProvider extends ServiceProvider
{





protected $listen = [];






protected $subscribe = [];






protected $observers = [];






protected static $shouldDiscoverEvents = true;






protected static $eventDiscoveryPaths;






public function register()
{
$this->booting(function () {
$events = $this->getEvents();

foreach ($events as $event => $listeners) {
foreach (array_unique($listeners, SORT_REGULAR) as $listener) {
Event::listen($event, $listener);
}
}

foreach ($this->subscribe as $subscriber) {
Event::subscribe($subscriber);
}

foreach ($this->observers as $model => $observers) {
$model::observe($observers);
}
});

$this->booted(function () {
$this->configureEmailVerification();
});
}






public function boot()
{

}






public function listens()
{
return $this->listen;
}






public function getEvents()
{
if ($this->app->eventsAreCached()) {
$cache = require $this->app->getCachedEventsPath();

return $cache[get_class($this)] ?? [];
} else {
return array_merge_recursive(
$this->discoveredEvents(),
$this->listens()
);
}
}






protected function discoveredEvents()
{
return $this->shouldDiscoverEvents()
? $this->discoverEvents()
: [];
}






public function shouldDiscoverEvents()
{
return get_class($this) === __CLASS__ && static::$shouldDiscoverEvents === true;
}






public function discoverEvents()
{
return (new LazyCollection($this->discoverEventsWithin()))
->flatMap(function ($directory) {
return glob($directory, GLOB_ONLYDIR);
})
->reject(function ($directory) {
return ! is_dir($directory);
})
->pipe(fn ($directories) => DiscoverEvents::within(
$directories->all(),
$this->eventDiscoveryBasePath(),
));
}






protected function discoverEventsWithin()
{
return static::$eventDiscoveryPaths ?: [
$this->app->path('Listeners'),
];
}







public static function addEventDiscoveryPaths(iterable|string $paths)
{
static::$eventDiscoveryPaths = (new LazyCollection(static::$eventDiscoveryPaths))
->merge(is_string($paths) ? [$paths] : $paths)
->unique()
->values();
}







public static function setEventDiscoveryPaths(iterable $paths)
{
static::$eventDiscoveryPaths = $paths;
}






protected function eventDiscoveryBasePath()
{
return base_path();
}






public static function disableEventDiscovery()
{
static::$shouldDiscoverEvents = false;
}






protected function configureEmailVerification()
{
if (! isset($this->listen[Registered::class]) ||
! in_array(SendEmailVerificationNotification::class, Arr::wrap($this->listen[Registered::class]))) {
Event::listen(Registered::class, SendEmailVerificationNotification::class);
}
}
}
