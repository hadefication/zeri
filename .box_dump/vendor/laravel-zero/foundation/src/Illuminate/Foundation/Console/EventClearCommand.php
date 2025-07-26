<?php

namespace Illuminate\Foundation\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'event:clear')]
class EventClearCommand extends Command
{





protected $name = 'event:clear';






protected $description = 'Clear all cached events and listeners';






protected $files;






public function __construct(Filesystem $files)
{
parent::__construct();

$this->files = $files;
}








public function handle()
{
$this->files->delete($this->laravel->getCachedEventsPath());

$this->components->info('Cached events cleared successfully.');
}
}
