<?php

namespace Illuminate\Foundation\Console;

use Illuminate\Console\Command;
use Illuminate\Foundation\Support\Providers\EventServiceProvider;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'event:generate')]
class EventGenerateCommand extends Command
{





protected $name = 'event:generate';






protected $description = 'Generate the missing events and listeners based on registration';






protected $hidden = true;






public function handle()
{
$providers = $this->laravel->getProviders(EventServiceProvider::class);

foreach ($providers as $provider) {
foreach ($provider->listens() as $event => $listeners) {
$this->makeEventAndListeners($event, $listeners);
}
}

$this->components->info('Events and listeners generated successfully.');
}








protected function makeEventAndListeners($event, $listeners)
{
if (! str_contains($event, '\\')) {
return;
}

$this->callSilent('make:event', ['name' => $event]);

$this->makeListeners($event, $listeners);
}








protected function makeListeners($event, $listeners)
{
foreach ($listeners as $listener) {
$listener = preg_replace('/@.+$/', '', $listener);

$this->callSilent('make:listener', array_filter(
['name' => $listener, '--event' => $event]
));
}
}
}
