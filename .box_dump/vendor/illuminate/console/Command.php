<?php

namespace Illuminate\Console;

use Illuminate\Console\View\Components\Factory;
use Illuminate\Contracts\Console\Isolatable;
use Illuminate\Support\Traits\Macroable;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

class Command extends SymfonyCommand
{
use Concerns\CallsCommands,
Concerns\ConfiguresPrompts,
Concerns\HasParameters,
Concerns\InteractsWithIO,
Concerns\InteractsWithSignals,
Concerns\PromptsForMissingInput,
Macroable;






protected $laravel;






protected $signature;






protected $name;






protected $description = '';






protected $help = '';






protected $hidden = false;






protected $isolated = false;






protected $isolatedExitCode = self::SUCCESS;






protected $aliases;




public function __construct()
{



if (isset($this->signature)) {
$this->configureUsingFluentDefinition();
} else {
parent::__construct($this->name);
}




if (! empty($this->description)) {
$this->setDescription($this->description);
}

if (! empty($this->help)) {
$this->setHelp($this->help);
}

$this->setHidden($this->isHidden());

if (isset($this->aliases)) {
$this->setAliases((array) $this->aliases);
}

if (! isset($this->signature)) {
$this->specifyParameters();
}

if ($this instanceof Isolatable) {
$this->configureIsolation();
}
}






protected function configureUsingFluentDefinition()
{
[$name, $arguments, $options] = Parser::parse($this->signature);

parent::__construct($this->name = $name);




$this->getDefinition()->addArguments($arguments);
$this->getDefinition()->addOptions($options);
}






protected function configureIsolation()
{
$this->getDefinition()->addOption(new InputOption(
'isolated',
null,
InputOption::VALUE_OPTIONAL,
'Do not run the command if another instance of the command is already running',
$this->isolated
));
}








#[\Override]
public function run(InputInterface $input, OutputInterface $output): int
{
$this->output = $output instanceof OutputStyle ? $output : $this->laravel->make(
OutputStyle::class, ['input' => $input, 'output' => $output]
);

$this->components = $this->laravel->make(Factory::class, ['output' => $this->output]);

$this->configurePrompts($input);

try {
return parent::run(
$this->input = $input, $this->output
);
} finally {
$this->untrap();
}
}







#[\Override]
protected function execute(InputInterface $input, OutputInterface $output): int
{
if ($this instanceof Isolatable && $this->option('isolated') !== false &&
! $this->commandIsolationMutex()->create($this)) {
$this->comment(sprintf(
'The [%s] command is already running.', $this->getName()
));

return (int) (is_numeric($this->option('isolated'))
? $this->option('isolated')
: $this->isolatedExitCode);
}

$method = method_exists($this, 'handle') ? 'handle' : '__invoke';

try {
return (int) $this->laravel->call([$this, $method]);
} catch (ManuallyFailedException $e) {
$this->components->error($e->getMessage());

return static::FAILURE;
} finally {
if ($this instanceof Isolatable && $this->option('isolated') !== false) {
$this->commandIsolationMutex()->forget($this);
}
}
}






protected function commandIsolationMutex()
{
return $this->laravel->bound(CommandMutex::class)
? $this->laravel->make(CommandMutex::class)
: $this->laravel->make(CacheCommandMutex::class);
}







protected function resolveCommand($command)
{
if (is_string($command)) {
if (! class_exists($command)) {
return $this->getApplication()->find($command);
}

$command = $this->laravel->make($command);
}

if ($command instanceof SymfonyCommand) {
$command->setApplication($this->getApplication());
}

if ($command instanceof self) {
$command->setLaravel($this->getLaravel());
}

return $command;
}









public function fail(Throwable|string|null $exception = null)
{
if (is_null($exception)) {
$exception = 'Command failed manually.';
}

if (is_string($exception)) {
$exception = new ManuallyFailedException($exception);
}

throw $exception;
}






#[\Override]
public function isHidden(): bool
{
return $this->hidden;
}




#[\Override]
public function setHidden(bool $hidden = true): static
{
parent::setHidden($this->hidden = $hidden);

return $this;
}






public function getLaravel()
{
return $this->laravel;
}







public function setLaravel($laravel)
{
$this->laravel = $laravel;
}
}
