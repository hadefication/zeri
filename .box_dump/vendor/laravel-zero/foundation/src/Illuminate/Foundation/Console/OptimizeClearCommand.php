<?php

namespace Illuminate\Foundation\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputOption;

#[AsCommand(name: 'optimize:clear')]
class OptimizeClearCommand extends Command
{





protected $name = 'optimize:clear';






protected $description = 'Remove the cached bootstrap files';






public function handle()
{
$this->components->info('Clearing cached bootstrap files.');

$exceptions = Collection::wrap(explode(',', $this->option('except') ?? ''))
->map(fn ($except) => trim($except))
->filter()
->unique()
->flip();

$tasks = Collection::wrap($this->getOptimizeClearTasks())
->reject(fn ($command, $key) => $exceptions->hasAny([$command, $key]))
->toArray();

foreach ($tasks as $description => $command) {
$this->components->task($description, fn () => $this->callSilently($command) == 0);
}

$this->newLine();
}






public function getOptimizeClearTasks()
{
return [
'config' => 'config:clear',
'cache' => 'cache:clear',
'compiled' => 'clear-compiled',
'events' => 'event:clear',
'routes' => 'route:clear',
'views' => 'view:clear',
...ServiceProvider::$optimizeClearCommands,
];
}






protected function getOptions()
{
return [
['except', 'e', InputOption::VALUE_OPTIONAL, 'The commands to skip'],
];
}
}
