<?php

namespace Illuminate\Console\Scheduling;

use Exception;
use Illuminate\Console\Application;
use Illuminate\Console\Command;
use Illuminate\Console\Events\ScheduledTaskFailed;
use Illuminate\Console\Events\ScheduledTaskFinished;
use Illuminate\Console\Events\ScheduledTaskSkipped;
use Illuminate\Console\Events\ScheduledTaskStarting;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Sleep;
use Symfony\Component\Console\Attribute\AsCommand;
use Throwable;

#[AsCommand(name: 'schedule:run')]
class ScheduleRunCommand extends Command
{





protected $signature = 'schedule:run {--whisper : Do not output message indicating that no jobs were ready to run}';






protected $description = 'Run the scheduled commands';






protected $schedule;






protected $startedAt;






protected $eventsRan = false;






protected $dispatcher;






protected $handler;






protected $cache;






protected $phpBinary;




public function __construct()
{
$this->startedAt = Date::now();

parent::__construct();
}










public function handle(Schedule $schedule, Dispatcher $dispatcher, Cache $cache, ExceptionHandler $handler)
{
$this->schedule = $schedule;
$this->dispatcher = $dispatcher;
$this->cache = $cache;
$this->handler = $handler;
$this->phpBinary = Application::phpBinary();

$events = $this->schedule->dueEvents($this->laravel);

if ($events->contains->isRepeatable()) {
$this->clearInterruptSignal();
}

foreach ($events as $event) {
if (! $event->filtersPass($this->laravel)) {
$this->dispatcher->dispatch(new ScheduledTaskSkipped($event));

continue;
}

if (! $this->eventsRan) {
$this->newLine();
}

if ($event->onOneServer) {
$this->runSingleServerEvent($event);
} else {
$this->runEvent($event);
}

$this->eventsRan = true;
}

if ($events->contains->isRepeatable()) {
$this->repeatEvents($events->filter->isRepeatable());
}

if (! $this->eventsRan) {
if (! $this->option('whisper')) {
$this->components->info('No scheduled commands are ready to run.');
}
} else {
$this->newLine();
}
}







protected function runSingleServerEvent($event)
{
if ($this->schedule->serverShouldRun($event, $this->startedAt)) {
$this->runEvent($event);
} else {
$this->components->info(sprintf(
'Skipping [%s], as command already run on another server.', $event->getSummaryForDisplay()
));
}
}







protected function runEvent($event)
{
$summary = $event->getSummaryForDisplay();

$command = $event instanceof CallbackEvent
? $summary
: trim(str_replace($this->phpBinary, '', $event->command));

$description = sprintf(
'<fg=gray>%s</> Running [%s]%s',
Carbon::now()->format('Y-m-d H:i:s'),
$command,
$event->runInBackground ? ' in background' : '',
);

$this->components->task($description, function () use ($event) {
$this->dispatcher->dispatch(new ScheduledTaskStarting($event));

$start = microtime(true);

try {
$event->run($this->laravel);

$this->dispatcher->dispatch(new ScheduledTaskFinished(
$event,
round(microtime(true) - $start, 2)
));

$this->eventsRan = true;

if ($event->exitCode != 0 && ! $event->runInBackground) {
throw new Exception("Scheduled command [{$event->command}] failed with exit code [{$event->exitCode}].");
}
} catch (Throwable $e) {
$this->dispatcher->dispatch(new ScheduledTaskFailed($event, $e));

$this->handler->report($e);
}

return $event->exitCode == 0;
});

if (! $event instanceof CallbackEvent) {
$this->components->bulletList([
$event->getSummaryForDisplay(),
]);
}
}







protected function repeatEvents($events)
{
$hasEnteredMaintenanceMode = false;

while (Date::now()->lte($this->startedAt->endOfMinute())) {
foreach ($events as $event) {
if ($this->shouldInterrupt()) {
return;
}

if (! $event->shouldRepeatNow()) {
continue;
}

$hasEnteredMaintenanceMode = $hasEnteredMaintenanceMode || $this->laravel->isDownForMaintenance();

if ($hasEnteredMaintenanceMode && ! $event->runsInMaintenanceMode()) {
continue;
}

if (! $event->filtersPass($this->laravel)) {
$this->dispatcher->dispatch(new ScheduledTaskSkipped($event));

continue;
}

if ($event->onOneServer) {
$this->runSingleServerEvent($event);
} else {
$this->runEvent($event);
}

$this->eventsRan = true;
}

Sleep::usleep(100000);
}
}






protected function shouldInterrupt()
{
return $this->cache->get('illuminate:schedule:interrupt', false);
}






protected function clearInterruptSignal()
{
$this->cache->forget('illuminate:schedule:interrupt');
}
}
