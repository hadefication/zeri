<?php

namespace Illuminate\Process;

use Illuminate\Support\Collection;
use InvalidArgumentException;

/**
@mixin
@mixin
*/
class Pool
{





protected $factory;






protected $callback;






protected $pendingProcesses = [];







public function __construct(Factory $factory, callable $callback)
{
$this->factory = $factory;
$this->callback = $callback;
}







public function as(string $key)
{
return tap($this->factory->newPendingProcess(), function ($pendingProcess) use ($key) {
$this->pendingProcesses[$key] = $pendingProcess;
});
}







public function start(?callable $output = null)
{
call_user_func($this->callback, $this);

return new InvokedProcessPool(
(new Collection($this->pendingProcesses))
->each(function ($pendingProcess) {
if (! $pendingProcess instanceof PendingProcess) {
throw new InvalidArgumentException('Process pool must only contain pending processes.');
}
})
->mapWithKeys(function ($pendingProcess, $key) use ($output) {
return [$key => $pendingProcess->start(output: $output ? function ($type, $buffer) use ($key, $output) {
$output($type, $buffer, $key);
} : null)];
})
->all()
);
}






public function run()
{
return $this->wait();
}






public function wait()
{
return $this->start()->wait();
}








public function __call($method, $parameters)
{
return tap($this->factory->{$method}(...$parameters), function ($pendingProcess) {
$this->pendingProcesses[] = $pendingProcess;
});
}
}
