<?php

namespace Illuminate\Foundation\Console;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use LogicException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use function Laravel\Prompts\suggest;

#[AsCommand(name: 'make:policy')]
class PolicyMakeCommand extends GeneratorCommand
{





protected $name = 'make:policy';






protected $description = 'Create a new policy class';






protected $type = 'Policy';







protected function buildClass($name)
{
$stub = $this->replaceUserNamespace(
parent::buildClass($name)
);

$model = $this->option('model');

return $model ? $this->replaceModel($stub, $model) : $stub;
}







protected function replaceUserNamespace($stub)
{
$model = $this->userProviderModel();

if (! $model) {
return $stub;
}

return str_replace(
$this->rootNamespace().'User',
$model,
$stub
);
}








protected function userProviderModel()
{
$config = $this->laravel['config'];

$guard = $this->option('guard') ?: $config->get('auth.defaults.guard');

if (is_null($guardProvider = $config->get('auth.guards.'.$guard.'.provider'))) {
throw new LogicException('The ['.$guard.'] guard is not defined in your "auth" configuration file.');
}

if (! $config->get('auth.providers.'.$guardProvider.'.model')) {
return 'App\\Models\\User';
}

return $config->get(
'auth.providers.'.$guardProvider.'.model'
);
}








protected function replaceModel($stub, $model)
{
$model = str_replace('/', '\\', $model);

if (str_starts_with($model, '\\')) {
$namespacedModel = trim($model, '\\');
} else {
$namespacedModel = $this->qualifyModel($model);
}

$model = class_basename(trim($model, '\\'));

$dummyUser = class_basename($this->userProviderModel());

$dummyModel = Str::camel($model) === 'user' ? 'model' : $model;

$replace = [
'NamespacedDummyModel' => $namespacedModel,
'{{ namespacedModel }}' => $namespacedModel,
'{{namespacedModel}}' => $namespacedModel,
'DummyModel' => $model,
'{{ model }}' => $model,
'{{model}}' => $model,
'dummyModel' => Str::camel($dummyModel),
'{{ modelVariable }}' => Str::camel($dummyModel),
'{{modelVariable}}' => Str::camel($dummyModel),
'DummyUser' => $dummyUser,
'{{ user }}' => $dummyUser,
'{{user}}' => $dummyUser,
'$user' => '$'.Str::camel($dummyUser),
];

$stub = str_replace(
array_keys($replace), array_values($replace), $stub
);

return preg_replace(
vsprintf('/use %s;[\r\n]+use %s;/', [
preg_quote($namespacedModel, '/'),
preg_quote($namespacedModel, '/'),
]),
"use {$namespacedModel};",
$stub
);
}






protected function getStub()
{
return $this->option('model')
? $this->resolveStubPath('/stubs/policy.stub')
: $this->resolveStubPath('/stubs/policy.plain.stub');
}







protected function resolveStubPath($stub)
{
return file_exists($customPath = $this->laravel->basePath(trim($stub, '/')))
? $customPath
: __DIR__.$stub;
}







protected function getDefaultNamespace($rootNamespace)
{
return $rootNamespace.'\Policies';
}






protected function getOptions()
{
return [
['force', 'f', InputOption::VALUE_NONE, 'Create the class even if the policy already exists'],
['model', 'm', InputOption::VALUE_OPTIONAL, 'The model that the policy applies to'],
['guard', 'g', InputOption::VALUE_OPTIONAL, 'The guard that the policy relies on'],
];
}








protected function afterPromptingForMissingArguments(InputInterface $input, OutputInterface $output)
{
if ($this->isReservedName($this->getNameInput()) || $this->didReceiveOptions($input)) {
return;
}

$model = suggest(
'What model should this policy apply to? (Optional)',
$this->possibleModels(),
);

if ($model) {
$input->setOption('model', $model);
}
}
}
