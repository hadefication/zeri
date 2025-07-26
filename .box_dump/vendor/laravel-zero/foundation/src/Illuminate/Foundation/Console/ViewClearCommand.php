<?php

namespace Illuminate\Foundation\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use RuntimeException;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'view:clear')]
class ViewClearCommand extends Command
{





protected $name = 'view:clear';






protected $description = 'Clear all compiled view files';






protected $files;






public function __construct(Filesystem $files)
{
parent::__construct();

$this->files = $files;
}








public function handle()
{
$path = $this->laravel['config']['view.compiled'];

if (! $path) {
throw new RuntimeException('View path not found.');
}

$this->laravel['view.engine.resolver']
->resolve('blade')
->forgetCompiledOrNotExpired();

foreach ($this->files->glob("{$path}/*") as $view) {
$this->files->delete($view);
}

$this->components->info('Compiled views cleared successfully.');
}
}
