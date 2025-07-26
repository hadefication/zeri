<?php

namespace Illuminate\Console;




class Signals
{





protected $registry;






protected $previousHandlers;






protected static $availabilityResolver;






public function __construct($registry)
{
$this->registry = $registry;

$this->previousHandlers = $this->getHandlers();
}








public function register($signal, $callback)
{
$this->previousHandlers[$signal] ??= $this->initializeSignal($signal);

with($this->getHandlers(), function ($handlers) use ($signal) {
$handlers[$signal] ??= $this->initializeSignal($signal);

$this->setHandlers($handlers);
});

$this->registry->register($signal, $callback);

with($this->getHandlers(), function ($handlers) use ($signal) {
$lastHandlerInserted = array_pop($handlers[$signal]);

array_unshift($handlers[$signal], $lastHandlerInserted);

$this->setHandlers($handlers);
});
}






protected function initializeSignal($signal)
{
return is_callable($existingHandler = pcntl_signal_get_handler($signal))
? [$existingHandler]
: null;
}






public function unregister()
{
$previousHandlers = $this->previousHandlers;

foreach ($previousHandlers as $signal => $handler) {
if (is_null($handler)) {
pcntl_signal($signal, SIG_DFL);

unset($previousHandlers[$signal]);
}
}

$this->setHandlers($previousHandlers);
}







public static function whenAvailable($callback)
{
$resolver = static::$availabilityResolver;

if ($resolver()) {
$callback();
}
}






protected function getHandlers()
{
return (fn () => $this->signalHandlers)
->call($this->registry);
}







protected function setHandlers($handlers)
{
(fn () => $this->signalHandlers = $handlers)
->call($this->registry);
}







public static function resolveAvailabilityUsing($resolver)
{
static::$availabilityResolver = $resolver;
}
}
