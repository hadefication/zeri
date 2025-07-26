<?php

namespace Illuminate\Foundation\Console;

use Illuminate\Console\Command;
use Illuminate\Foundation\Support\Providers\EventServiceProvider;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'event:cache')]
class EventCacheCommand extends Command
{





protected $signature = 'event:cache';






protected $description = "Discover and cache the application's events and listeners";






public function handle()
{
$this->callSilent('event:clear');

file_put_contents(
$this->laravel->getCachedEventsPath(),
'<?php return '.var_export($this->getEvents(), true).';'
);

$this->components->info('Events cached successfully.');
}






protected function getEvents()
{
$events = [];

foreach ($this->laravel->getProviders(EventServiceProvider::class) as $provider) {
$providerEvents = array_merge_recursive($provider->shouldDiscoverEvents() ? $provider->discoverEvents() : [], $provider->listens());

$events[get_class($provider)] = $providerEvents;
}

return $events;
}
}
