<?php

namespace Illuminate\Foundation\Console;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputOption;

#[AsCommand(name: 'make:request')]
class RequestMakeCommand extends GeneratorCommand
{





protected $name = 'make:request';






protected $description = 'Create a new form request class';






protected $type = 'Request';






protected function getStub()
{
return $this->resolveStubPath('/stubs/request.stub');
}







protected function resolveStubPath($stub)
{
return file_exists($customPath = $this->laravel->basePath(trim($stub, '/')))
? $customPath
: __DIR__.$stub;
}







protected function getDefaultNamespace($rootNamespace)
{
return $rootNamespace.'\Http\Requests';
}






protected function getOptions()
{
return [
['force', 'f', InputOption::VALUE_NONE, 'Create the class even if the request already exists'],
];
}
}
