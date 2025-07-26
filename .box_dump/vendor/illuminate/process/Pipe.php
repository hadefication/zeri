<?php

namespace Illuminate\Process;

use Illuminate\Support\Collection;
use InvalidArgumentException;

/**
@mixin
@mixin
*/
class Pipe
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







public function run(?callable $output = null)
{
call_user_func($this->callback, $this);

return (new Collection($this->pendingProcesses))
->reduce(function ($previousProcessResult, $pendingProcess, $key) use ($output) {
if (! $pendingProcess instanceof PendingProcess) {
throw new InvalidArgumentException('Process pipe must only contain pending processes.');
}

if ($previousProcessResult && $previousProcessResult->failed()) {
return $previousProcessResult;
}

return $pendingProcess->when(
$previousProcessResult,
fn () => $pendingProcess->input($previousProcessResult->output())
)->run(output: $output ? function ($type, $buffer) use ($key, $output) {
$output($type, $buffer, $key);
} : null);
});
}








public function __call($method, $parameters)
{
return tap($this->factory->{$method}(...$parameters), function ($pendingProcess) {
$this->pendingProcesses[] = $pendingProcess;
});
}
}
