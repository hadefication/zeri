<?php

namespace Illuminate\Foundation\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'config:clear')]
class ConfigClearCommand extends Command
{





protected $name = 'config:clear';






protected $description = 'Remove the configuration cache file';






protected $files;






public function __construct(Filesystem $files)
{
parent::__construct();

$this->files = $files;
}






public function handle()
{
$this->files->delete($this->laravel->getCachedConfigPath());

$this->components->info('Configuration cache cleared successfully.');
}
}
