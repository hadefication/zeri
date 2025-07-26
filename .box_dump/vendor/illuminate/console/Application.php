<?php

namespace Illuminate\Console;

use Closure;
use Illuminate\Console\Events\ArtisanStarting;
use Illuminate\Contracts\Console\Application as ApplicationContract;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\ProcessUtils;
use ReflectionClass;
use Symfony\Component\Console\Application as SymfonyApplication;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Exception\CommandNotFoundException;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\BufferedOutput;

use function Illuminate\Support\artisan_binary;
use function Illuminate\Support\php_binary;

class Application extends SymfonyApplication implements ApplicationContract
{





protected $laravel;






protected $events;






protected $lastOutput;






protected static $bootstrappers = [];






protected $commandMap = [];








public function __construct(Container $laravel, Dispatcher $events, $version)
{
parent::__construct('Laravel Framework', $version);

$this->laravel = $laravel;
$this->events = $events;
$this->setAutoExit(false);
$this->setCatchExceptions(false);

$this->events->dispatch(new ArtisanStarting($this));

$this->bootstrap();
}






public static function phpBinary()
{
return ProcessUtils::escapeArgument(php_binary());
}






public static function artisanBinary()
{
return ProcessUtils::escapeArgument(artisan_binary());
}







public static function formatCommandString($string)
{
return sprintf('%s %s %s', static::phpBinary(), static::artisanBinary(), $string);
}







public static function starting(Closure $callback)
{
static::$bootstrappers[] = $callback;
}






protected function bootstrap()
{
foreach (static::$bootstrappers as $bootstrapper) {
$bootstrapper($this);
}
}






public static function forgetBootstrappers()
{
static::$bootstrappers = [];
}











public function call($command, array $parameters = [], $outputBuffer = null)
{
[$command, $input] = $this->parseCommand($command, $parameters);

if (! $this->has($command)) {
throw new CommandNotFoundException(sprintf('The command "%s" does not exist.', $command));
}

return $this->run(
$input, $this->lastOutput = $outputBuffer ?: new BufferedOutput
);
}








protected function parseCommand($command, $parameters)
{
if (is_subclass_of($command, SymfonyCommand::class)) {
$callingClass = true;

$command = $this->laravel->make($command)->getName();
}

if (! isset($callingClass) && empty($parameters)) {
$command = $this->getCommandName($input = new StringInput($command));
} else {
array_unshift($parameters, $command);

$input = new ArrayInput($parameters);
}

return [$command, $input];
}






public function output()
{
return $this->lastOutput && method_exists($this->lastOutput, 'fetch')
? $this->lastOutput->fetch()
: '';
}







#[\Override]
public function addCommands(array $commands): void
{
foreach ($commands as $command) {
$this->add($command);
}
}







#[\Override]
public function add(SymfonyCommand $command): ?SymfonyCommand
{
if ($command instanceof Command) {
$command->setLaravel($this->laravel);
}

return $this->addToParent($command);
}







protected function addToParent(SymfonyCommand $command)
{
return parent::add($command);
}







public function resolve($command)
{
if (is_subclass_of($command, SymfonyCommand::class)) {
$attribute = (new ReflectionClass($command))->getAttributes(AsCommand::class);

$commandName = ! empty($attribute) ? $attribute[0]->newInstance()->name : null;

if (! is_null($commandName)) {
foreach (explode('|', $commandName) as $name) {
$this->commandMap[$name] = $command;
}

return null;
}
}

if ($command instanceof Command) {
return $this->add($command);
}

return $this->add($this->laravel->make($command));
}







public function resolveCommands($commands)
{
$commands = is_array($commands) ? $commands : func_get_args();

foreach ($commands as $command) {
$this->resolve($command);
}

return $this;
}






public function setContainerCommandLoader()
{
$this->setCommandLoader(new ContainerCommandLoader($this->laravel, $this->commandMap));

return $this;
}








#[\Override]
protected function getDefaultInputDefinition(): InputDefinition
{
return tap(parent::getDefaultInputDefinition(), function ($definition) {
$definition->addOption($this->getEnvironmentOption());
});
}






protected function getEnvironmentOption()
{
$message = 'The environment the command should run under';

return new InputOption('--env', null, InputOption::VALUE_OPTIONAL, $message);
}






public function getLaravel()
{
return $this->laravel;
}
}
