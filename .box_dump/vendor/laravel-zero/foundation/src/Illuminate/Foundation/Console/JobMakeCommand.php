<?php

namespace Illuminate\Foundation\Console;

use Illuminate\Console\Concerns\CreatesMatchingTest;
use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputOption;

#[AsCommand(name: 'make:job')]
class JobMakeCommand extends GeneratorCommand
{
use CreatesMatchingTest;






protected $name = 'make:job';






protected $description = 'Create a new job class';






protected $type = 'Job';






protected function getStub()
{
return $this->option('sync')
? $this->resolveStubPath('/stubs/job.stub')
: $this->resolveStubPath('/stubs/job.queued.stub');
}







protected function resolveStubPath($stub)
{
return file_exists($customPath = $this->laravel->basePath(trim($stub, '/')))
? $customPath
: __DIR__.$stub;
}







protected function getDefaultNamespace($rootNamespace)
{
return $rootNamespace.'\Jobs';
}






protected function getOptions()
{
return [
['force', 'f', InputOption::VALUE_NONE, 'Create the class even if the job already exists'],
['sync', null, InputOption::VALUE_NONE, 'Indicates that job should be synchronous'],
];
}
}
