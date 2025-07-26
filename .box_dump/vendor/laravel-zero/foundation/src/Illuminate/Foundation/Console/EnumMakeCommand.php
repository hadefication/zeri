<?php

namespace Illuminate\Foundation\Console;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use function Laravel\Prompts\select;

#[AsCommand(name: 'make:enum')]
class EnumMakeCommand extends GeneratorCommand
{





protected $name = 'make:enum';






protected $description = 'Create a new enum';






protected $type = 'Enum';






protected function getStub()
{
if ($this->option('string') || $this->option('int')) {
return $this->resolveStubPath('/stubs/enum.backed.stub');
}

return $this->resolveStubPath('/stubs/enum.stub');
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
is_dir(app_path('Enums')) => $rootNamespace.'\\Enums',
is_dir(app_path('Enumerations')) => $rootNamespace.'\\Enumerations',
default => $rootNamespace,
};
}









protected function buildClass($name)
{
if ($this->option('string') || $this->option('int')) {
return str_replace(
['{{ type }}'],
$this->option('string') ? 'string' : 'int',
parent::buildClass($name)
);
}

return parent::buildClass($name);
}








protected function afterPromptingForMissingArguments(InputInterface $input, OutputInterface $output)
{
if ($this->didReceiveOptions($input)) {
return;
}

$type = select('Which type of enum would you like?', [
'pure' => 'Pure enum',
'string' => 'Backed enum (String)',
'int' => 'Backed enum (Integer)',
]);

if ($type !== 'pure') {
$input->setOption($type, true);
}
}






protected function getOptions()
{
return [
['string', 's', InputOption::VALUE_NONE, 'Generate a string backed enum.'],
['int', 'i', InputOption::VALUE_NONE, 'Generate an integer backed enum.'],
['force', 'f', InputOption::VALUE_NONE, 'Create the enum even if the enum already exists'],
];
}
}
