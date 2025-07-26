<?php

namespace Illuminate\Foundation\Console;

use Illuminate\Console\GeneratorCommand;
use InvalidArgumentException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use function Laravel\Prompts\suggest;

#[AsCommand(name: 'make:observer')]
class ObserverMakeCommand extends GeneratorCommand
{





protected $name = 'make:observer';






protected $description = 'Create a new observer class';






protected $type = 'Observer';







protected function buildClass($name)
{
$stub = parent::buildClass($name);

$model = $this->option('model');

return $model ? $this->replaceModel($stub, $model) : $stub;
}








protected function replaceModel($stub, $model)
{
$modelClass = $this->parseModel($model);

$replace = [
'DummyFullModelClass' => $modelClass,
'{{ namespacedModel }}' => $modelClass,
'{{namespacedModel}}' => $modelClass,
'DummyModelClass' => class_basename($modelClass),
'{{ model }}' => class_basename($modelClass),
'{{model}}' => class_basename($modelClass),
'DummyModelVariable' => lcfirst(class_basename($modelClass)),
'{{ modelVariable }}' => lcfirst(class_basename($modelClass)),
'{{modelVariable}}' => lcfirst(class_basename($modelClass)),
];

return str_replace(
array_keys($replace), array_values($replace), $stub
);
}









protected function parseModel($model)
{
if (preg_match('([^A-Za-z0-9_/\\\\])', $model)) {
throw new InvalidArgumentException('Model name contains invalid characters.');
}

return $this->qualifyModel($model);
}






protected function getStub()
{
return $this->option('model')
? $this->resolveStubPath('/stubs/observer.stub')
: $this->resolveStubPath('/stubs/observer.plain.stub');
}







protected function resolveStubPath($stub)
{
return file_exists($customPath = $this->laravel->basePath(trim($stub, '/')))
? $customPath
: __DIR__.$stub;
}







protected function getDefaultNamespace($rootNamespace)
{
return $rootNamespace.'\Observers';
}






protected function getOptions()
{
return [
['force', 'f', InputOption::VALUE_NONE, 'Create the class even if the observer already exists'],
['model', 'm', InputOption::VALUE_OPTIONAL, 'The model that the observer applies to'],
];
}








protected function afterPromptingForMissingArguments(InputInterface $input, OutputInterface $output)
{
if ($this->isReservedName($this->getNameInput()) || $this->didReceiveOptions($input)) {
return;
}

$model = suggest(
'What model should this observer apply to? (Optional)',
$this->possibleModels(),
);

if ($model) {
$input->setOption('model', $model);
}
}
}
