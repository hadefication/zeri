<?php

declare(strict_types=1);










namespace LaravelZero\Framework\Components\Updater;

use LaravelZero\Framework\Commands\Command;

class SelfUpdateCommand extends Command
{



protected $name = 'self-update';




protected $description = 'Self-update the installed application';




public function handle(Updater $updater)
{
$this->output->title('Checking for a new version...');

$updater->update($this->output);
}
}
