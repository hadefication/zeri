<?php

namespace Illuminate\Foundation\Console;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputOption;

#[AsCommand(name: 'make:scope')]
class ScopeMakeCommand extends GeneratorCommand
{





protected $name = 'make:scope';






protected $description = 'Create a new scope class';






protected $type = 'Scope';






protected function getStub()
{
return $this->resolveStubPath('/stubs/scope.stub');
}







protected function resolveStubPath($stub)
{
return file_exists($customPath = $this->laravel->basePath(trim($stub, '/')))
? $customPath
: __DIR__.$stub;
}







protected function getDefaultNamespace($rootNamespace)
{
return is_dir(app_path('Models')) ? $rootNamespace.'\\Models\\Scopes' : $rootNamespace.'\Scopes';
}






protected function getOptions()
{
return [
['force', 'f', InputOption::VALUE_NONE, 'Create the class even if the scope already exists'],
];
}
}
