<?php

namespace Illuminate\Foundation\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'clear-compiled')]
class ClearCompiledCommand extends Command
{





protected $name = 'clear-compiled';






protected $description = 'Remove the compiled class file';






public function handle()
{
if (is_file($servicesPath = $this->laravel->getCachedServicesPath())) {
@unlink($servicesPath);
}

if (is_file($packagesPath = $this->laravel->getCachedPackagesPath())) {
@unlink($packagesPath);
}

$this->components->info('Compiled services and packages files removed successfully.');
}
}
