<?php

declare(strict_types=1);










namespace LaravelZero\Framework\Commands;

use Illuminate\Console\Command as BaseCommand;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Application;
use LaravelZero\Framework\Providers\CommandRecorder\CommandRecorderRepository;

use function func_get_args;
use function str_repeat;
use function strlen;

abstract class Command extends BaseCommand
{





protected $app;


protected $description = '';




public function schedule(Schedule $schedule) {}




public function setLaravel($laravel): void
{
parent::setLaravel($this->app = $laravel);
}





public function task(string $title = '', $task = null): bool
{
return $this->__call('task', func_get_args());
}




public function title(string $title): Command
{
$size = strlen($title);
$spaces = str_repeat(' ', $size);

$this->output->newLine();
$this->output->writeln("<bg=blue;fg=white>$spaces$spaces$spaces</>");
$this->output->writeln("<bg=blue;fg=white>$spaces$title$spaces</>");
$this->output->writeln("<bg=blue;fg=white>$spaces$spaces$spaces</>");
$this->output->newLine();

return $this;
}




public function call($command, array $arguments = [])
{
resolve(CommandRecorderRepository::class)->create($command, $arguments);

return parent::call($command, $arguments);
}




public function callSilent($command, array $arguments = [])
{
resolve(CommandRecorderRepository::class)->create($command, $arguments, CommandRecorderRepository::MODE_SILENT);

return parent::callSilent($command, $arguments);
}






public function setHidden(bool $hidden = true): static
{
parent::setHidden($this->hidden = $hidden);

return $this;
}
}
