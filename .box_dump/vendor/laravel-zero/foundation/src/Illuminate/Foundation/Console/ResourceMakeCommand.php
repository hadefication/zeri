<?php

namespace Illuminate\Foundation\Console;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputOption;

#[AsCommand(name: 'make:resource')]
class ResourceMakeCommand extends GeneratorCommand
{





protected $name = 'make:resource';






protected $description = 'Create a new resource';






protected $type = 'Resource';






public function handle()
{
if ($this->collection()) {
$this->type = 'Resource collection';
}

parent::handle();
}






protected function getStub()
{
return $this->collection()
? $this->resolveStubPath('/stubs/resource-collection.stub')
: $this->resolveStubPath('/stubs/resource.stub');
}






protected function collection()
{
return $this->option('collection') ||
str_ends_with($this->argument('name'), 'Collection');
}







protected function resolveStubPath($stub)
{
return file_exists($customPath = $this->laravel->basePath(trim($stub, '/')))
? $customPath
: __DIR__.$stub;
}







protected function getDefaultNamespace($rootNamespace)
{
return $rootNamespace.'\Http\Resources';
}






protected function getOptions()
{
return [
['force', 'f', InputOption::VALUE_NONE, 'Create the class even if the resource already exists'],
['collection', 'c', InputOption::VALUE_NONE, 'Create a resource collection'],
];
}
}
