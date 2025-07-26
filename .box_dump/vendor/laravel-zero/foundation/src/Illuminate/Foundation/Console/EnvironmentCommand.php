<?php

namespace Illuminate\Foundation\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'env')]
class EnvironmentCommand extends Command
{





protected $name = 'env';






protected $description = 'Display the current framework environment';






public function handle()
{
$this->components->info(sprintf(
'The application environment is [%s].',
$this->laravel['env'],
));
}
}
