<?php

namespace Illuminate\Foundation\Console;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputOption;

#[AsCommand(name: 'make:trait')]
class TraitMakeCommand extends GeneratorCommand
{





protected $name = 'make:trait';






protected $description = 'Create a new trait';






protected $type = 'Trait';






protected function getStub()
{
return $this->resolveStubPath('/stubs/trait.stub');
}







protected function resolveStubPath($stub)
{
return file_exists($customPath = $this->laravel->basePath(trim($stub, '/')))
? $customPath
: __DIR__.$stub;
}







protected function getDefaultNamespace($rootNamespace)
{
return match (true) {
is_dir(app_path('Concerns')) => $rootNamespace.'\\Concerns',
is_dir(app_path('Traits')) => $rootNamespace.'\\Traits',
default => $rootNamespace,
};
}






protected function getOptions()
{
return [
['force', 'f', InputOption::VALUE_NONE, 'Create the trait even if the trait already exists'],
];
}
}
