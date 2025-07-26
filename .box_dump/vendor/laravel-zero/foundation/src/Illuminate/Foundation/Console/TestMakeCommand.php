<?php

namespace Illuminate\Foundation\Console;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use function Laravel\Prompts\select;

#[AsCommand(name: 'make:test')]
class TestMakeCommand extends GeneratorCommand
{





protected $name = 'make:test';






protected $description = 'Create a new test class';






protected $type = 'Test';






protected function getStub()
{
$suffix = $this->option('unit') ? '.unit.stub' : '.stub';

return $this->usingPest()
? $this->resolveStubPath('/stubs/pest'.$suffix)
: $this->resolveStubPath('/stubs/test'.$suffix);
}







protected function resolveStubPath($stub)
{
return file_exists($customPath = $this->laravel->basePath(trim($stub, '/')))
? $customPath
: __DIR__.$stub;
}







protected function getPath($name)
{
$name = Str::replaceFirst($this->rootNamespace(), '', $name);

return base_path('tests').str_replace('\\', '/', $name).'.php';
}







protected function getDefaultNamespace($rootNamespace)
{
if ($this->option('unit')) {
return $rootNamespace.'\Unit';
} else {
return $rootNamespace.'\Feature';
}
}






protected function rootNamespace()
{
return 'Tests';
}






protected function getOptions()
{
return [
['force', 'f', InputOption::VALUE_NONE, 'Create the test even if the test already exists'],
['unit', 'u', InputOption::VALUE_NONE, 'Create a unit test'],
['pest', null, InputOption::VALUE_NONE, 'Create a Pest test'],
['phpunit', null, InputOption::VALUE_NONE, 'Create a PHPUnit test'],
];
}








protected function afterPromptingForMissingArguments(InputInterface $input, OutputInterface $output)
{
if ($this->isReservedName($this->getNameInput()) || $this->didReceiveOptions($input)) {
return;
}

$type = select('Which type of test would you like?', [
'feature' => 'Feature',
'unit' => 'Unit',
]);

match ($type) {
'feature' => null,
'unit' => $input->setOption('unit', true),
};
}






protected function usingPest()
{
if ($this->option('phpunit')) {
return false;
}

return $this->option('pest') ||
(function_exists('\Pest\\version') &&
file_exists(base_path('tests').'/Pest.php'));
}
}
