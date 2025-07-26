<?php

namespace Illuminate\Foundation\Console;

use Illuminate\Console\Concerns\CreatesMatchingTest;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use function Laravel\Prompts\suggest;

#[AsCommand(name: 'make:listener')]
class ListenerMakeCommand extends GeneratorCommand
{
use CreatesMatchingTest;






protected $name = 'make:listener';






protected $description = 'Create a new event listener class';






protected $type = 'Listener';







protected function buildClass($name)
{
$event = $this->option('event') ?? '';

if (! Str::startsWith($event, [
$this->laravel->getNamespace(),
'Illuminate',
'\\',
])) {
$event = $this->laravel->getNamespace().'Events\\'.str_replace('/', '\\', $event);
}

$stub = str_replace(
['DummyEvent', '{{ event }}'], class_basename($event), parent::buildClass($name)
);

return str_replace(
['DummyFullEvent', '{{ eventNamespace }}'], trim($event, '\\'), $stub
);
}







protected function resolveStubPath($stub)
{
return file_exists($customPath = $this->laravel->basePath(trim($stub, '/')))
? $customPath
: __DIR__.$stub;
}






protected function getStub()
{
if ($this->option('queued')) {
return $this->option('event')
? $this->resolveStubPath('/stubs/listener.typed.queued.stub')
: $this->resolveStubPath('/stubs/listener.queued.stub');
}

return $this->option('event')
? $this->resolveStubPath('/stubs/listener.typed.stub')
: $this->resolveStubPath('/stubs/listener.stub');
}







protected function alreadyExists($rawName)
{
return class_exists($this->qualifyClass($rawName));
}







protected function getDefaultNamespace($rootNamespace)
{
return $rootNamespace.'\Listeners';
}






protected function getOptions()
{
return [
['event', 'e', InputOption::VALUE_OPTIONAL, 'The event class being listened for'],
['force', 'f', InputOption::VALUE_NONE, 'Create the class even if the listener already exists'],
['queued', null, InputOption::VALUE_NONE, 'Indicates the event listener should be queued'],
];
}








protected function afterPromptingForMissingArguments(InputInterface $input, OutputInterface $output)
{
if ($this->isReservedName($this->getNameInput()) || $this->didReceiveOptions($input)) {
return;
}

$event = suggest(
'What event should be listened for? (Optional)',
$this->possibleEvents(),
);

if ($event) {
$input->setOption('event', $event);
}
}
}
