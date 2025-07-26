<?php

namespace Illuminate\Foundation\Console;

use Illuminate\Console\Concerns\CreatesMatchingTest;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use function Laravel\Prompts\multiselect;

#[AsCommand(name: 'make:model')]
class ModelMakeCommand extends GeneratorCommand
{
use CreatesMatchingTest;






protected $name = 'make:model';






protected $description = 'Create a new Eloquent model class';






protected $type = 'Model';






public function handle()
{
if (parent::handle() === false && ! $this->option('force')) {
return false;
}

if ($this->option('all')) {
$this->input->setOption('factory', true);
$this->input->setOption('seed', true);
$this->input->setOption('migration', true);
$this->input->setOption('controller', true);
$this->input->setOption('policy', true);
$this->input->setOption('resource', true);
}

if ($this->option('factory')) {
$this->createFactory();
}

if ($this->option('migration')) {
$this->createMigration();
}

if ($this->option('seed')) {
$this->createSeeder();
}

if ($this->option('controller') || $this->option('resource') || $this->option('api')) {
$this->createController();
} elseif ($this->option('requests')) {
$this->createFormRequests();
}

if ($this->option('policy')) {
$this->createPolicy();
}
}






protected function createFactory()
{
$factory = Str::studly($this->argument('name'));

$this->call('make:factory', [
'name' => "{$factory}Factory",
'--model' => $this->qualifyClass($this->getNameInput()),
]);
}






protected function createMigration()
{
$table = Str::snake(Str::pluralStudly(class_basename($this->argument('name'))));

if ($this->option('pivot')) {
$table = Str::singular($table);
}

$this->call('make:migration', [
'name' => "create_{$table}_table",
'--create' => $table,
]);
}






protected function createSeeder()
{
$seeder = Str::studly(class_basename($this->argument('name')));

$this->call('make:seeder', [
'name' => "{$seeder}Seeder",
]);
}






protected function createController()
{
$controller = Str::studly(class_basename($this->argument('name')));

$modelName = $this->qualifyClass($this->getNameInput());

$this->call('make:controller', array_filter([
'name' => "{$controller}Controller",
'--model' => $this->option('resource') || $this->option('api') ? $modelName : null,
'--api' => $this->option('api'),
'--requests' => $this->option('requests') || $this->option('all'),
'--test' => $this->option('test'),
'--pest' => $this->option('pest'),
]));
}






protected function createFormRequests()
{
$request = Str::studly(class_basename($this->argument('name')));

$this->call('make:request', [
'name' => "Store{$request}Request",
]);

$this->call('make:request', [
'name' => "Update{$request}Request",
]);
}






protected function createPolicy()
{
$policy = Str::studly(class_basename($this->argument('name')));

$this->call('make:policy', [
'name' => "{$policy}Policy",
'--model' => $this->qualifyClass($this->getNameInput()),
]);
}






protected function getStub()
{
if ($this->option('pivot')) {
return $this->resolveStubPath('/stubs/model.pivot.stub');
}

if ($this->option('morph-pivot')) {
return $this->resolveStubPath('/stubs/model.morph-pivot.stub');
}

return $this->resolveStubPath('/stubs/model.stub');
}







protected function resolveStubPath($stub)
{
return file_exists($customPath = $this->laravel->basePath(trim($stub, '/')))
? $customPath
: __DIR__.$stub;
}







protected function getDefaultNamespace($rootNamespace)
{
return is_dir(app_path('Models')) ? $rootNamespace.'\\Models' : $rootNamespace;
}









protected function buildClass($name)
{
$replace = $this->buildFactoryReplacements();

return str_replace(
array_keys($replace), array_values($replace), parent::buildClass($name)
);
}






protected function buildFactoryReplacements()
{
$replacements = [];

if ($this->option('factory') || $this->option('all')) {
$modelPath = Str::of($this->argument('name'))->studly()->replace('/', '\\')->toString();

$factoryNamespace = '\\Database\\Factories\\'.$modelPath.'Factory';

$factoryCode = <<<EOT
            /** @use HasFactory<$factoryNamespace> */
                use HasFactory;
            EOT;

$replacements['{{ factory }}'] = $factoryCode;
$replacements['{{ factoryImport }}'] = 'use Illuminate\Database\Eloquent\Factories\HasFactory;';
} else {
$replacements['{{ factory }}'] = '//';
$replacements["{{ factoryImport }}\n"] = '';
$replacements["{{ factoryImport }}\r\n"] = '';
}

return $replacements;
}






protected function getOptions()
{
return [
['all', 'a', InputOption::VALUE_NONE, 'Generate a migration, seeder, factory, policy, resource controller, and form request classes for the model'],
['controller', 'c', InputOption::VALUE_NONE, 'Create a new controller for the model'],
['factory', 'f', InputOption::VALUE_NONE, 'Create a new factory for the model'],
['force', null, InputOption::VALUE_NONE, 'Create the class even if the model already exists'],
['migration', 'm', InputOption::VALUE_NONE, 'Create a new migration file for the model'],
['morph-pivot', null, InputOption::VALUE_NONE, 'Indicates if the generated model should be a custom polymorphic intermediate table model'],
['policy', null, InputOption::VALUE_NONE, 'Create a new policy for the model'],
['seed', 's', InputOption::VALUE_NONE, 'Create a new seeder for the model'],
['pivot', 'p', InputOption::VALUE_NONE, 'Indicates if the generated model should be a custom intermediate table model'],
['resource', 'r', InputOption::VALUE_NONE, 'Indicates if the generated controller should be a resource controller'],
['api', null, InputOption::VALUE_NONE, 'Indicates if the generated controller should be an API resource controller'],
['requests', 'R', InputOption::VALUE_NONE, 'Create new form request classes and use them in the resource controller'],
];
}








protected function afterPromptingForMissingArguments(InputInterface $input, OutputInterface $output)
{
if ($this->isReservedName($this->getNameInput()) || $this->didReceiveOptions($input)) {
return;
}

(new Collection(multiselect('Would you like any of the following?', [
'seed' => 'Database Seeder',
'factory' => 'Factory',
'requests' => 'Form Requests',
'migration' => 'Migration',
'policy' => 'Policy',
'resource' => 'Resource Controller',
])))->each(fn ($option) => $input->setOption($option, true));
}
}
