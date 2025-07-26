<?php

namespace Illuminate\Foundation\Console;

use Closure;
use Illuminate\Console\Command;
use Illuminate\Console\ManuallyFailedException;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Traits\ForwardsCalls;
use ReflectionFunction;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
@mixin
*/
class ClosureCommand extends Command
{
use ForwardsCalls;






protected $callback;






protected $description = '';







public function __construct($signature, Closure $callback)
{
$this->callback = $callback;
$this->signature = $signature;

parent::__construct();
}








protected function execute(InputInterface $input, OutputInterface $output): int
{
$inputs = array_merge($input->getArguments(), $input->getOptions());

$parameters = [];

foreach ((new ReflectionFunction($this->callback))->getParameters() as $parameter) {
if (isset($inputs[$parameter->getName()])) {
$parameters[$parameter->getName()] = $inputs[$parameter->getName()];
}
}

try {
return (int) $this->laravel->call(
$this->callback->bindTo($this, $this), $parameters
);
} catch (ManuallyFailedException $e) {
$this->components->error($e->getMessage());

return static::FAILURE;
}
}







public function purpose($description)
{
return $this->describe($description);
}







public function describe($description)
{
$this->setDescription($description);

return $this;
}







public function schedule($parameters = [])
{
return Schedule::command($this->name, $parameters);
}










public function __call($method, $parameters)
{
return $this->forwardCallTo($this->schedule(), $method, $parameters);
}
}
