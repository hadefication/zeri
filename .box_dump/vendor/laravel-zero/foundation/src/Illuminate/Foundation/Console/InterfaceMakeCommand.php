<?php

namespace Illuminate\Foundation\Console;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputOption;

#[AsCommand(name: 'make:interface')]
class InterfaceMakeCommand extends GeneratorCommand
{





protected $name = 'make:interface';






protected $description = 'Create a new interface';






protected $type = 'Interface';






protected function getStub()
{
return __DIR__.'/stubs/interface.stub';
}







protected function getDefaultNamespace($rootNamespace)
{
return match (true) {
is_dir(app_path('Contracts')) => $rootNamespace.'\\Contracts',
is_dir(app_path('Interfaces')) => $rootNamespace.'\\Interfaces',
default => $rootNamespace,
};
}






protected function getOptions()
{
return [
['force', 'f', InputOption::VALUE_NONE, 'Create the interface even if the interface already exists'],
];
}
}
