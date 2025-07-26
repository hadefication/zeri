<?php

namespace Illuminate\Foundation\Console;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputOption;

#[AsCommand(name: 'make:cast')]
class CastMakeCommand extends GeneratorCommand
{





protected $name = 'make:cast';






protected $description = 'Create a new custom Eloquent cast class';






protected $type = 'Cast';






protected function getStub()
{
return $this->option('inbound')
? $this->resolveStubPath('/stubs/cast.inbound.stub')
: $this->resolveStubPath('/stubs/cast.stub');
}







protected function resolveStubPath($stub)
{
return file_exists($customPath = $this->laravel->basePath(trim($stub, '/')))
? $customPath
: __DIR__.$stub;
}







protected function getDefaultNamespace($rootNamespace)
{
return $rootNamespace.'\Casts';
}






protected function getOptions()
{
return [
['force', 'f', InputOption::VALUE_NONE, 'Create the class even if the cast already exists'],
['inbound', null, InputOption::VALUE_NONE, 'Generate an inbound cast class'],
];
}
}
