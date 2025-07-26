<?php

namespace Illuminate\Console\Scheduling;

use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Reflector;
use InvalidArgumentException;
use LogicException;
use RuntimeException;
use Throwable;

class CallbackEvent extends Event
{





protected $callback;






protected $parameters;






protected $result;






protected $exception;











public function __construct(EventMutex $mutex, $callback, array $parameters = [], $timezone = null)
{
if (! is_string($callback) && ! Reflector::isCallable($callback)) {
throw new InvalidArgumentException(
'Invalid scheduled callback event. Must be a string or callable.'
);
}

$this->mutex = $mutex;
$this->callback = $callback;
$this->parameters = $parameters;
$this->timezone = $timezone;
}









public function run(Container $container)
{
parent::run($container);

if ($this->exception) {
throw $this->exception;
}

return $this->result;
}






public function shouldSkipDueToOverlapping()
{
return $this->description && parent::shouldSkipDueToOverlapping();
}








public function runInBackground()
{
throw new RuntimeException('Scheduled closures can not be run in the background.');
}







protected function execute($container)
{
try {
$this->result = is_object($this->callback)
? $container->call([$this->callback, '__invoke'], $this->parameters)
: $container->call($this->callback, $this->parameters);

return $this->result === false ? 1 : 0;
} catch (Throwable $e) {
$this->exception = $e;

return 1;
}
}











public function withoutOverlapping($expiresAt = 1440)
{
if (! isset($this->description)) {
throw new LogicException(
"A scheduled event name is required to prevent overlapping. Use the 'name' method before 'withoutOverlapping'."
);
}

return parent::withoutOverlapping($expiresAt);
}








public function onOneServer()
{
if (! isset($this->description)) {
throw new LogicException(
"A scheduled event name is required to only run on one server. Use the 'name' method before 'onOneServer'."
);
}

return parent::onOneServer();
}






public function getSummaryForDisplay()
{
if (is_string($this->description)) {
return $this->description;
}

return is_string($this->callback) ? $this->callback : 'Callback';
}






public function mutexName()
{
return 'framework/schedule-'.sha1($this->description ?? '');
}






protected function removeMutex()
{
if ($this->description) {
parent::removeMutex();
}
}
}
