<?php

namespace Illuminate\Foundation\Console;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputOption;

#[AsCommand(name: 'make:rule')]
class RuleMakeCommand extends GeneratorCommand
{





protected $name = 'make:rule';






protected $description = 'Create a new validation rule';






protected $type = 'Rule';









protected function buildClass($name)
{
return str_replace(
'{{ ruleType }}',
$this->option('implicit') ? 'ImplicitRule' : 'Rule',
parent::buildClass($name)
);
}






protected function getStub()
{
$stub = $this->option('implicit')
? '/stubs/rule.implicit.stub'
: '/stubs/rule.stub';

return file_exists($customPath = $this->laravel->basePath(trim($stub, '/')))
? $customPath
: __DIR__.$stub;
}







protected function getDefaultNamespace($rootNamespace)
{
return $rootNamespace.'\Rules';
}






protected function getOptions()
{
return [
['force', 'f', InputOption::VALUE_NONE, 'Create the class even if the rule already exists'],
['implicit', 'i', InputOption::VALUE_NONE, 'Generate an implicit rule'],
];
}
}
