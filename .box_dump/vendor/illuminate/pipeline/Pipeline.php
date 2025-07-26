<?php

namespace Illuminate\Pipeline;

use Closure;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Pipeline\Pipeline as PipelineContract;
use Illuminate\Support\Traits\Conditionable;
use RuntimeException;
use Throwable;

class Pipeline implements PipelineContract
{
use Conditionable;






protected $container;






protected $passable;






protected $pipes = [];






protected $method = 'handle';






protected $finally;






public function __construct(?Container $container = null)
{
$this->container = $container;
}







public function send($passable)
{
$this->passable = $passable;

return $this;
}







public function through($pipes)
{
$this->pipes = is_array($pipes) ? $pipes : func_get_args();

return $this;
}







public function pipe($pipes)
{
array_push($this->pipes, ...(is_array($pipes) ? $pipes : func_get_args()));

return $this;
}







public function via($method)
{
$this->method = $method;

return $this;
}







public function then(Closure $destination)
{
$pipeline = array_reduce(
array_reverse($this->pipes()), $this->carry(), $this->prepareDestination($destination)
);

try {
return $pipeline($this->passable);
} finally {
if ($this->finally) {
($this->finally)($this->passable);
}
}
}






public function thenReturn()
{
return $this->then(function ($passable) {
return $passable;
});
}







public function finally(Closure $callback)
{
$this->finally = $callback;

return $this;
}







protected function prepareDestination(Closure $destination)
{
return function ($passable) use ($destination) {
try {
return $destination($passable);
} catch (Throwable $e) {
return $this->handleException($passable, $e);
}
};
}






protected function carry()
{
return function ($stack, $pipe) {
return function ($passable) use ($stack, $pipe) {
try {
if (is_callable($pipe)) {



return $pipe($passable, $stack);
} elseif (! is_object($pipe)) {
[$name, $parameters] = $this->parsePipeString($pipe);




$pipe = $this->getContainer()->make($name);

$parameters = array_merge([$passable, $stack], $parameters);
} else {



$parameters = [$passable, $stack];
}

$carry = method_exists($pipe, $this->method)
? $pipe->{$this->method}(...$parameters)
: $pipe(...$parameters);

return $this->handleCarry($carry);
} catch (Throwable $e) {
return $this->handleException($passable, $e);
}
};
};
}







protected function parsePipeString($pipe)
{
[$name, $parameters] = array_pad(explode(':', $pipe, 2), 2, null);

if (! is_null($parameters)) {
$parameters = explode(',', $parameters);
} else {
$parameters = [];
}

return [$name, $parameters];
}






protected function pipes()
{
return $this->pipes;
}








protected function getContainer()
{
if (! $this->container) {
throw new RuntimeException('A container instance has not been passed to the Pipeline.');
}

return $this->container;
}







public function setContainer(Container $container)
{
$this->container = $container;

return $this;
}







protected function handleCarry($carry)
{
return $carry;
}










protected function handleException($passable, Throwable $e)
{
throw $e;
}
}
