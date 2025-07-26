<?php

namespace Illuminate\Foundation\Console;

use Illuminate\Console\Concerns\CreatesMatchingTest;
use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputOption;

#[AsCommand(name: 'make:job-middleware')]
class JobMiddlewareMakeCommand extends GeneratorCommand
{
use CreatesMatchingTest;






protected $name = 'make:job-middleware';






protected $description = 'Create a new job middleware class';






protected $type = 'Middleware';






protected function getStub()
{
return $this->resolveStubPath('/stubs/job.middleware.stub');
}







protected function resolveStubPath($stub)
{
return file_exists($customPath = $this->laravel->basePath(trim($stub, '/')))
? $customPath
: __DIR__.$stub;
}







protected function getDefaultNamespace($rootNamespace)
{
return $rootNamespace.'\Jobs\Middleware';
}






protected function getOptions()
{
return [
['force', 'f', InputOption::VALUE_NONE, 'Create the class even if the job middleware already exists'],
];
}
}
