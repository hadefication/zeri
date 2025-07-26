<?php

namespace Illuminate\Console\Concerns;

use Illuminate\Support\Collection;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;

trait CallsCommands
{






abstract protected function resolveCommand($command);








public function call($command, array $arguments = [])
{
return $this->runCommand($command, $arguments, $this->output);
}








public function callSilent($command, array $arguments = [])
{
return $this->runCommand($command, $arguments, new NullOutput);
}








public function callSilently($command, array $arguments = [])
{
return $this->callSilent($command, $arguments);
}









protected function runCommand($command, array $arguments, OutputInterface $output)
{
$arguments['command'] = $command;

$result = $this->resolveCommand($command)->run(
$this->createInputFromArguments($arguments), $output
);

$this->restorePrompts();

return $result;
}







protected function createInputFromArguments(array $arguments)
{
return tap(new ArrayInput(array_merge($this->context(), $arguments)), function ($input) {
if ($input->getParameterOption('--no-interaction')) {
$input->setInteractive(false);
}
});
}






protected function context()
{
return (new Collection($this->option()))
->only([
'ansi',
'no-ansi',
'no-interaction',
'quiet',
'verbose',
])
->filter()
->mapWithKeys(fn ($value, $key) => ["--{$key}" => $value])
->all();
}
}
