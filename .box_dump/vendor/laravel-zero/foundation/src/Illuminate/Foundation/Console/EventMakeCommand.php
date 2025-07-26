<?php

namespace Illuminate\Foundation\Console;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputOption;

#[AsCommand(name: 'make:event')]
class EventMakeCommand extends GeneratorCommand
{





protected $name = 'make:event';






protected $description = 'Create a new event class';






protected $type = 'Event';







protected function alreadyExists($rawName)
{
return class_exists($rawName) ||
$this->files->exists($this->getPath($this->qualifyClass($rawName)));
}






protected function getStub()
{
return $this->resolveStubPath('/stubs/event.stub');
}







protected function resolveStubPath($stub)
{
return file_exists($customPath = $this->laravel->basePath(trim($stub, '/')))
? $customPath
: __DIR__.$stub;
}







protected function getDefaultNamespace($rootNamespace)
{
return $rootNamespace.'\Events';
}






protected function getOptions()
{
return [
['force', 'f', InputOption::VALUE_NONE, 'Create the class even if the event already exists'],
];
}
}
