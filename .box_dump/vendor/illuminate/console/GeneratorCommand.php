<?php

namespace Illuminate\Console;

use Illuminate\Console\Concerns\CreatesMatchingTest;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Finder\Finder;

abstract class GeneratorCommand extends Command implements PromptsForMissingInput
{





protected $files;






protected $type;






protected $reservedNames = [
'__halt_compiler',
'abstract',
'and',
'array',
'as',
'break',
'callable',
'case',
'catch',
'class',
'clone',
'const',
'continue',
'declare',
'default',
'die',
'do',
'echo',
'else',
'elseif',
'empty',
'enddeclare',
'endfor',
'endforeach',
'endif',
'endswitch',
'endwhile',
'enum',
'eval',
'exit',
'extends',
'false',
'final',
'finally',
'fn',
'for',
'foreach',
'function',
'global',
'goto',
'if',
'implements',
'include',
'include_once',
'instanceof',
'insteadof',
'interface',
'isset',
'list',
'match',
'namespace',
'new',
'or',
'parent',
'print',
'private',
'protected',
'public',
'readonly',
'require',
'require_once',
'return',
'self',
'static',
'switch',
'throw',
'trait',
'true',
'try',
'unset',
'use',
'var',
'while',
'xor',
'yield',
'__CLASS__',
'__DIR__',
'__FILE__',
'__FUNCTION__',
'__LINE__',
'__METHOD__',
'__NAMESPACE__',
'__TRAIT__',
];






public function __construct(Filesystem $files)
{
parent::__construct();

if (in_array(CreatesMatchingTest::class, class_uses_recursive($this))) {
$this->addTestOptions();
}

$this->files = $files;
}






abstract protected function getStub();








public function handle()
{



if ($this->isReservedName($this->getNameInput())) {
$this->components->error('The name "'.$this->getNameInput().'" is reserved by PHP.');

return false;
}

$name = $this->qualifyClass($this->getNameInput());

$path = $this->getPath($name);




if ((! $this->hasOption('force') ||
! $this->option('force')) &&
$this->alreadyExists($this->getNameInput())) {
$this->components->error($this->type.' already exists.');

return false;
}




$this->makeDirectory($path);

$this->files->put($path, $this->sortImports($this->buildClass($name)));

$info = $this->type;

if (in_array(CreatesMatchingTest::class, class_uses_recursive($this))) {
$this->handleTestCreation($path);
}

if (windows_os()) {
$path = str_replace('/', '\\', $path);
}

$this->components->info(sprintf('%s [%s] created successfully.', $info, $path));
}







protected function qualifyClass($name)
{
$name = ltrim($name, '\\/');

$name = str_replace('/', '\\', $name);

$rootNamespace = $this->rootNamespace();

if (Str::startsWith($name, $rootNamespace)) {
return $name;
}

return $this->qualifyClass(
$this->getDefaultNamespace(trim($rootNamespace, '\\')).'\\'.$name
);
}







protected function qualifyModel(string $model)
{
$model = ltrim($model, '\\/');

$model = str_replace('/', '\\', $model);

$rootNamespace = $this->rootNamespace();

if (Str::startsWith($model, $rootNamespace)) {
return $model;
}

return is_dir(app_path('Models'))
? $rootNamespace.'Models\\'.$model
: $rootNamespace.$model;
}






protected function possibleModels()
{
$modelPath = is_dir(app_path('Models')) ? app_path('Models') : app_path();

return (new Collection(Finder::create()->files()->depth(0)->in($modelPath)))
->map(fn ($file) => $file->getBasename('.php'))
->sort()
->values()
->all();
}






protected function possibleEvents()
{
$eventPath = app_path('Events');

if (! is_dir($eventPath)) {
return [];
}

return (new Collection(Finder::create()->files()->depth(0)->in($eventPath)))
->map(fn ($file) => $file->getBasename('.php'))
->sort()
->values()
->all();
}







protected function getDefaultNamespace($rootNamespace)
{
return $rootNamespace;
}







protected function alreadyExists($rawName)
{
return $this->files->exists($this->getPath($this->qualifyClass($rawName)));
}







protected function getPath($name)
{
$name = Str::replaceFirst($this->rootNamespace(), '', $name);

return $this->laravel['path'].'/'.str_replace('\\', '/', $name).'.php';
}







protected function makeDirectory($path)
{
if (! $this->files->isDirectory(dirname($path))) {
$this->files->makeDirectory(dirname($path), 0777, true, true);
}

return $path;
}









protected function buildClass($name)
{
$stub = $this->files->get($this->getStub());

return $this->replaceNamespace($stub, $name)->replaceClass($stub, $name);
}








protected function replaceNamespace(&$stub, $name)
{
$searches = [
['DummyNamespace', 'DummyRootNamespace', 'NamespacedDummyUserModel'],
['{{ namespace }}', '{{ rootNamespace }}', '{{ namespacedUserModel }}'],
['{{namespace}}', '{{rootNamespace}}', '{{namespacedUserModel}}'],
];

foreach ($searches as $search) {
$stub = str_replace(
$search,
[$this->getNamespace($name), $this->rootNamespace(), $this->userProviderModel()],
$stub
);
}

return $this;
}







protected function getNamespace($name)
{
return trim(implode('\\', array_slice(explode('\\', $name), 0, -1)), '\\');
}








protected function replaceClass($stub, $name)
{
$class = str_replace($this->getNamespace($name).'\\', '', $name);

return str_replace(['DummyClass', '{{ class }}', '{{class}}'], $class, $stub);
}







protected function sortImports($stub)
{
if (preg_match('/(?P<imports>(?:^use [^;{]+;$\n?)+)/m', $stub, $match)) {
$imports = explode("\n", trim($match['imports']));

sort($imports);

return str_replace(trim($match['imports']), implode("\n", $imports), $stub);
}

return $stub;
}






protected function getNameInput()
{
$name = trim($this->argument('name'));

if (Str::endsWith($name, '.php')) {
return Str::substr($name, 0, -4);
}

return $name;
}






protected function rootNamespace()
{
return $this->laravel->getNamespace();
}






protected function userProviderModel()
{
$config = $this->laravel['config'];

$provider = $config->get('auth.guards.'.$config->get('auth.defaults.guard').'.provider');

return $config->get("auth.providers.{$provider}.model");
}







protected function isReservedName($name)
{
return in_array(
strtolower($name),
(new Collection($this->reservedNames))
->transform(fn ($name) => strtolower($name))
->all()
);
}







protected function viewPath($path = '')
{
$views = $this->laravel['config']['view.paths'][0] ?? resource_path('views');

return $views.($path ? DIRECTORY_SEPARATOR.$path : $path);
}






protected function getArguments()
{
return [
['name', InputArgument::REQUIRED, 'The name of the '.strtolower($this->type)],
];
}






protected function promptForMissingArgumentsUsing()
{
return [
'name' => [
'What should the '.strtolower($this->type).' be named?',
match ($this->type) {
'Cast' => 'E.g. Json',
'Channel' => 'E.g. OrderChannel',
'Console command' => 'E.g. SendEmails',
'Component' => 'E.g. Alert',
'Controller' => 'E.g. UserController',
'Event' => 'E.g. PodcastProcessed',
'Exception' => 'E.g. InvalidOrderException',
'Factory' => 'E.g. PostFactory',
'Job' => 'E.g. ProcessPodcast',
'Listener' => 'E.g. SendPodcastNotification',
'Mailable' => 'E.g. OrderShipped',
'Middleware' => 'E.g. EnsureTokenIsValid',
'Model' => 'E.g. Flight',
'Notification' => 'E.g. InvoicePaid',
'Observer' => 'E.g. UserObserver',
'Policy' => 'E.g. PostPolicy',
'Provider' => 'E.g. ElasticServiceProvider',
'Request' => 'E.g. StorePodcastRequest',
'Resource' => 'E.g. UserResource',
'Rule' => 'E.g. Uppercase',
'Scope' => 'E.g. TrendingScope',
'Seeder' => 'E.g. UserSeeder',
'Test' => 'E.g. UserTest',
default => '',
},
],
];
}
}
