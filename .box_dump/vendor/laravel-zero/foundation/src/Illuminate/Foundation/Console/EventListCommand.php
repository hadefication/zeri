<?php

namespace Illuminate\Foundation\Console;

use Closure;
use Illuminate\Console\Command;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;
use ReflectionFunction;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'event:list')]
class EventListCommand extends Command
{





protected $signature = 'event:list
                            {--event= : Filter the events by name}
                            {--json : Output the events and listeners as JSON}';






protected $description = "List the application's events and listeners";






protected static $eventsResolver;






public function handle()
{
$events = $this->getEvents()->sortKeys();

if ($events->isEmpty()) {
if ($this->option('json')) {
$this->output->writeln('[]');
} else {
$this->components->info("Your application doesn't have any events matching the given criteria.");
}

return;
}

if ($this->option('json')) {
$this->displayJson($events);
} else {
$this->displayForCli($events);
}
}







protected function displayJson(Collection $events)
{
$data = $events->map(function ($listeners, $event) {
return [
'event' => strip_tags($this->appendEventInterfaces($event)),
'listeners' => collect($listeners)->map(fn ($listener) => strip_tags($listener))->values()->all(),
];
})->values();

$this->output->writeln($data->toJson());
}







protected function displayForCli(Collection $events)
{
$this->newLine();

$events->each(function ($listeners, $event) {
$this->components->twoColumnDetail($this->appendEventInterfaces($event));
$this->components->bulletList($listeners);
});

$this->newLine();
}






protected function getEvents()
{
$events = new Collection($this->getListenersOnDispatcher());

if ($this->filteringByEvent()) {
$events = $this->filterEvents($events);
}

return $events;
}






protected function getListenersOnDispatcher()
{
$events = [];

foreach ($this->getRawListeners() as $event => $rawListeners) {
foreach ($rawListeners as $rawListener) {
if (is_string($rawListener)) {
$events[$event][] = $this->appendListenerInterfaces($rawListener);
} elseif ($rawListener instanceof Closure) {
$events[$event][] = $this->stringifyClosure($rawListener);
} elseif (is_array($rawListener) && count($rawListener) === 2) {
if (is_object($rawListener[0])) {
$rawListener[0] = get_class($rawListener[0]);
}

$events[$event][] = $this->appendListenerInterfaces(implode('@', $rawListener));
}
}
}

return $events;
}







protected function appendEventInterfaces($event)
{
if (! class_exists($event)) {
return $event;
}

$interfaces = class_implements($event);

if (in_array(ShouldBroadcast::class, $interfaces)) {
$event .= ' <fg=bright-blue>(ShouldBroadcast)</>';
}

return $event;
}







protected function appendListenerInterfaces($listener)
{
$listener = explode('@', $listener);

$interfaces = class_implements($listener[0]);

$listener = implode('@', $listener);

if (in_array(ShouldQueue::class, $interfaces)) {
$listener .= ' <fg=bright-blue>(ShouldQueue)</>';
}

return $listener;
}







protected function stringifyClosure(Closure $rawListener)
{
$reflection = new ReflectionFunction($rawListener);

$path = str_replace([base_path(), DIRECTORY_SEPARATOR], ['', '/'], $reflection->getFileName() ?: '');

return 'Closure at: '.$path.':'.$reflection->getStartLine();
}







protected function filterEvents($events)
{
if (! $eventName = $this->option('event')) {
return $events;
}

return $events->filter(
fn ($listeners, $event) => str_contains($event, $eventName)
);
}






protected function filteringByEvent()
{
return ! empty($this->option('event'));
}






protected function getRawListeners()
{
return $this->getEventsDispatcher()->getRawListeners();
}






public function getEventsDispatcher()
{
return is_null(self::$eventsResolver)
? $this->getLaravel()->make('events')
: call_user_func(self::$eventsResolver);
}







public static function resolveEventsUsing($resolver)
{
static::$eventsResolver = $resolver;
}
}
