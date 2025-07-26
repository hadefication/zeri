<?php

namespace Illuminate\Foundation\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Finder\Finder;

use function Laravel\Prompts\select;

#[AsCommand(name: 'config:publish')]
class ConfigPublishCommand extends Command
{





protected $signature = 'config:publish
                    {name? : The name of the configuration file to publish}
                    {--all : Publish all configuration files}
                    {--force : Overwrite any existing configuration files}';






protected $description = 'Publish configuration files to your application';






public function handle()
{
$config = $this->getBaseConfigurationFiles();

if (is_null($this->argument('name')) && $this->option('all')) {
foreach ($config as $key => $file) {
$this->publish($key, $file, $this->laravel->configPath().'/'.$key.'.php');
}

return;
}

$name = (string) (is_null($this->argument('name')) ? select(
label: 'Which configuration file would you like to publish?',
options: (new Collection($config))->map(function (string $path) {
return basename($path, '.php');
}),
) : $this->argument('name'));

if (! is_null($name) && ! isset($config[$name])) {
$this->components->error('Unrecognized configuration file.');

return 1;
}

$this->publish($name, $config[$name], $this->laravel->configPath().'/'.$name.'.php');
}









protected function publish(string $name, string $file, string $destination)
{
if (file_exists($destination) && ! $this->option('force')) {
$this->components->error("The '{$name}' configuration file already exists.");

return;
}

copy($file, $destination);

$this->components->info("Published '{$name}' configuration file.");
}






protected function getBaseConfigurationFiles()
{
$config = [];

$shouldMergeConfiguration = $this->laravel->shouldMergeFrameworkConfiguration();

foreach (Finder::create()->files()->name('*.php')->in(__DIR__.'/../../../../config') as $file) {
$name = basename($file->getRealPath(), '.php');

$config[$name] = ($shouldMergeConfiguration === true && file_exists($stubPath = (__DIR__.'/../../../../config-stubs/'.$name.'.php')))
? $stubPath
: $file->getRealPath();
}

return (new Collection($config))->sortKeys()->all();
}
}
