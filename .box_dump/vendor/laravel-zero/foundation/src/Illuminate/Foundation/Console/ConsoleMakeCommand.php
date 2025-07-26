<?php

namespace Illuminate\Foundation\Console;

use Illuminate\Console\Concerns\CreatesMatchingTest;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Stringable;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

#[AsCommand(name: 'make:command')]
class ConsoleMakeCommand extends GeneratorCommand
{
use CreatesMatchingTest;






protected $name = 'make:command';






protected $description = 'Create a new Artisan command';






protected $type = 'Console command';








protected function replaceClass($stub, $name)
{
$stub = parent::replaceClass($stub, $name);

$command = $this->option('command') ?: 'app:'.(new Stringable($name))->classBasename()->kebab()->value();

return str_replace(['dummy:command', '{{ command }}'], $command, $stub);
}






protected function getStub()
{
$relativePath = '/stubs/console.stub';

return file_exists($customPath = $this->laravel->basePath(trim($relativePath, '/')))
? $customPath
: __DIR__.$relativePath;
}







protected function getDefaultNamespace($rootNamespace)
{
return $rootNamespace.'\Console\Commands';
}






protected function getArguments()
{
return [
['name', InputArgument::REQUIRED, 'The name of the command'],
];
}






protected function getOptions()
{
return [
['force', 'f', InputOption::VALUE_NONE, 'Create the class even if the console command already exists'],
['command', null, InputOption::VALUE_OPTIONAL, 'The terminal command that will be used to invoke the class'],
];
}
}
