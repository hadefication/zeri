<?php

namespace Illuminate\Foundation\Console;

use Illuminate\Console\Concerns\CreatesMatchingTest;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputOption;

#[AsCommand(name: 'make:view')]
class ViewMakeCommand extends GeneratorCommand
{
use CreatesMatchingTest;






protected $description = 'Create a new view';






protected $name = 'make:view';






protected $type = 'View';









protected function buildClass($name)
{
$contents = parent::buildClass($name);

return str_replace(
'{{ quote }}',
Inspiring::quotes()->random(),
$contents,
);
}







protected function getPath($name)
{
return $this->viewPath(
$this->getNameInput().'.'.$this->option('extension'),
);
}






protected function getNameInput()
{
$name = trim($this->argument('name'));

$name = str_replace(['\\', '.'], '/', $name);

return $name;
}






protected function getStub()
{
return $this->resolveStubPath(
'/stubs/view.stub',
);
}







protected function resolveStubPath($stub)
{
return file_exists($customPath = $this->laravel->basePath(trim($stub, '/')))
? $customPath
: __DIR__.$stub;
}






protected function getTestPath()
{
return base_path(
Str::of($this->testClassFullyQualifiedName())
->replace('\\', '/')
->replaceFirst('Tests/Feature', 'tests/Feature')
->append('Test.php')
->value()
);
}






protected function handleTestCreation($path): bool
{
if (! $this->option('test') && ! $this->option('pest') && ! $this->option('phpunit')) {
return false;
}

$contents = preg_replace(
['/\{{ namespace \}}/', '/\{{ class \}}/', '/\{{ name \}}/'],
[$this->testNamespace(), $this->testClassName(), $this->testViewName()],
File::get($this->getTestStub()),
);

File::ensureDirectoryExists(dirname($this->getTestPath()), 0755, true);

$result = File::put($path = $this->getTestPath(), $contents);

$this->components->info(sprintf('%s [%s] created successfully.', 'Test', $path));

return $result !== false;
}






protected function testNamespace()
{
return Str::of($this->testClassFullyQualifiedName())
->beforeLast('\\')
->value();
}






protected function testClassName()
{
return Str::of($this->testClassFullyQualifiedName())
->afterLast('\\')
->append('Test')
->value();
}






protected function testClassFullyQualifiedName()
{
$name = Str::of(Str::lower($this->getNameInput()))->replace('.'.$this->option('extension'), '');

$namespacedName = Str::of(
(new Stringable($name))
->replace('/', ' ')
->explode(' ')
->map(fn ($part) => (new Stringable($part))->ucfirst())
->implode('\\')
)
->replace(['-', '_'], ' ')
->explode(' ')
->map(fn ($part) => (new Stringable($part))->ucfirst())
->implode('');

return 'Tests\\Feature\\View\\'.$namespacedName;
}






protected function getTestStub()
{
$stubName = 'view.'.($this->usingPest() ? 'pest' : 'test').'.stub';

return file_exists($customPath = $this->laravel->basePath("stubs/$stubName"))
? $customPath
: __DIR__.'/stubs/'.$stubName;
}






protected function testViewName()
{
return Str::of($this->getNameInput())
->replace('/', '.')
->lower()
->value();
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






protected function getOptions()
{
return [
['extension', null, InputOption::VALUE_OPTIONAL, 'The extension of the generated view', 'blade.php'],
['force', 'f', InputOption::VALUE_NONE, 'Create the view even if the view already exists'],
];
}
}
