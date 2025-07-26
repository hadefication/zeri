<?php

namespace Illuminate\Foundation\Console;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputOption;

#[AsCommand(name: 'make:class')]
class ClassMakeCommand extends GeneratorCommand
{





protected $name = 'make:class';






protected $description = 'Create a new class';






protected $type = 'Class';






protected function getStub()
{
return $this->option('invokable')
? $this->resolveStubPath('/stubs/class.invokable.stub')
: $this->resolveStubPath('/stubs/class.stub');
}







protected function resolveStubPath($stub)
{
return file_exists($customPath = $this->laravel->basePath(trim($stub, '/')))
? $customPath
: __DIR__.$stub;
}






protected function getOptions()
{
return [
['invokable', 'i', InputOption::VALUE_NONE, 'Generate a single method, invokable class'],
['force', 'f', InputOption::VALUE_NONE, 'Create the class even if the class already exists'],
];
}
}
