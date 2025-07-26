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

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\text;

#[AsCommand(name: 'make:notification')]
class NotificationMakeCommand extends GeneratorCommand
{
use CreatesMatchingTest;






protected $name = 'make:notification';






protected $description = 'Create a new notification class';






protected $type = 'Notification';






public function handle()
{
if (parent::handle() === false && ! $this->option('force')) {
return;
}

if ($this->option('markdown')) {
$this->writeMarkdownTemplate();
}
}






protected function writeMarkdownTemplate()
{
$path = $this->viewPath(
str_replace('.', '/', $this->option('markdown')).'.blade.php'
);

if (! $this->files->isDirectory(dirname($path))) {
$this->files->makeDirectory(dirname($path), 0755, true);
}

$this->files->put($path, file_get_contents(__DIR__.'/stubs/markdown.stub'));

$this->components->info(sprintf('%s [%s] created successfully.', 'Markdown', $path));
}







protected function buildClass($name)
{
$class = parent::buildClass($name);

if ($this->option('markdown')) {
$class = str_replace(['DummyView', '{{ view }}'], $this->option('markdown'), $class);
}

return $class;
}






protected function getStub()
{
return $this->option('markdown')
? $this->resolveStubPath('/stubs/markdown-notification.stub')
: $this->resolveStubPath('/stubs/notification.stub');
}







protected function resolveStubPath($stub)
{
return file_exists($customPath = $this->laravel->basePath(trim($stub, '/')))
? $customPath
: __DIR__.$stub;
}







protected function getDefaultNamespace($rootNamespace)
{
return $rootNamespace.'\Notifications';
}








protected function afterPromptingForMissingArguments(InputInterface $input, OutputInterface $output)
{
if ($this->didReceiveOptions($input)) {
return;
}

$wantsMarkdownView = confirm('Would you like to create a markdown view?');

if ($wantsMarkdownView) {
$defaultMarkdownView = (new Collection(explode('/', str_replace('\\', '/', $this->argument('name')))))
->map(fn ($path) => Str::kebab($path))
->prepend('mail')
->implode('.');

$markdownView = text('What should the markdown view be named?', default: $defaultMarkdownView);

$input->setOption('markdown', $markdownView);
}
}






protected function getOptions()
{
return [
['force', 'f', InputOption::VALUE_NONE, 'Create the class even if the notification already exists'],
['markdown', 'm', InputOption::VALUE_OPTIONAL, 'Create a new Markdown template for the notification'],
];
}
}
