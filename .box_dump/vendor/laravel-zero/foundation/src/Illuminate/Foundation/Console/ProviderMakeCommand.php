<?php

namespace Illuminate\Foundation\Console;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputOption;

#[AsCommand(name: 'make:provider')]
class ProviderMakeCommand extends GeneratorCommand
{





protected $name = 'make:provider';






protected $description = 'Create a new service provider class';






protected $type = 'Provider';








public function handle()
{
$result = parent::handle();

if ($result === false) {
return $result;
}

ServiceProvider::addProviderToBootstrapFile(
$this->qualifyClass($this->getNameInput()),
$this->laravel->getBootstrapProvidersPath(),
);

return $result;
}






protected function getStub()
{
return $this->resolveStubPath('/stubs/provider.stub');
}







protected function resolveStubPath($stub)
{
return file_exists($customPath = $this->laravel->basePath(trim($stub, '/')))
? $customPath
: __DIR__.$stub;
}







protected function getDefaultNamespace($rootNamespace)
{
return $rootNamespace.'\Providers';
}






protected function getOptions()
{
return [
['force', 'f', InputOption::VALUE_NONE, 'Create the class even if the provider already exists'],
];
}
}
