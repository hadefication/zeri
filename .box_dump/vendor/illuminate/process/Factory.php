<?php

namespace Illuminate\Process;

use Closure;
use Illuminate\Contracts\Process\ProcessResult as ProcessResultContract;
use Illuminate\Support\Collection;
use Illuminate\Support\Traits\Macroable;
use PHPUnit\Framework\Assert as PHPUnit;

class Factory
{
use Macroable {
__call as macroCall;
}






protected $recording = false;






protected $recorded = [];






protected $fakeHandlers = [];






protected $preventStrayProcesses = false;









public function result(array|string $output = '', array|string $errorOutput = '', int $exitCode = 0)
{
return new FakeProcessResult(
output: $output,
errorOutput: $errorOutput,
exitCode: $exitCode,
);
}






public function describe()
{
return new FakeProcessDescription;
}







public function sequence(array $processes = [])
{
return new FakeProcessSequence($processes);
}







public function fake(Closure|array|null $callback = null)
{
$this->recording = true;

if (is_null($callback)) {
$this->fakeHandlers = ['*' => fn () => new FakeProcessResult];

return $this;
}

if ($callback instanceof Closure) {
$this->fakeHandlers = ['*' => $callback];

return $this;
}

foreach ($callback as $command => $handler) {
$this->fakeHandlers[is_numeric($command) ? '*' : $command] = $handler instanceof Closure
? $handler
: fn () => $handler;
}

return $this;
}






public function isRecording()
{
return $this->recording;
}








public function recordIfRecording(PendingProcess $process, ProcessResultContract $result)
{
if ($this->isRecording()) {
$this->record($process, $result);
}

return $this;
}








public function record(PendingProcess $process, ProcessResultContract $result)
{
$this->recorded[] = [$process, $result];

return $this;
}







public function preventStrayProcesses(bool $prevent = true)
{
$this->preventStrayProcesses = $prevent;

return $this;
}






public function preventingStrayProcesses()
{
return $this->preventStrayProcesses;
}







public function assertRan(Closure|string $callback)
{
$callback = is_string($callback) ? fn ($process) => $process->command === $callback : $callback;

PHPUnit::assertTrue(
(new Collection($this->recorded))->filter(function ($pair) use ($callback) {
return $callback($pair[0], $pair[1]);
})->count() > 0,
'An expected process was not invoked.'
);

return $this;
}








public function assertRanTimes(Closure|string $callback, int $times = 1)
{
$callback = is_string($callback) ? fn ($process) => $process->command === $callback : $callback;

$count = (new Collection($this->recorded))
->filter(fn ($pair) => $callback($pair[0], $pair[1]))
->count();

PHPUnit::assertSame(
$times, $count,
"An expected process ran {$count} times instead of {$times} times."
);

return $this;
}







public function assertNotRan(Closure|string $callback)
{
$callback = is_string($callback) ? fn ($process) => $process->command === $callback : $callback;

PHPUnit::assertTrue(
(new Collection($this->recorded))->filter(function ($pair) use ($callback) {
return $callback($pair[0], $pair[1]);
})->count() === 0,
'An unexpected process was invoked.'
);

return $this;
}







public function assertDidntRun(Closure|string $callback)
{
return $this->assertNotRan($callback);
}






public function assertNothingRan()
{
PHPUnit::assertEmpty(
$this->recorded,
'An unexpected process was invoked.'
);

return $this;
}







public function pool(callable $callback)
{
return new Pool($this, $callback);
}







public function pipe(callable|array $callback, ?callable $output = null)
{
return is_array($callback)
? (new Pipe($this, fn ($pipe) => (new Collection($callback))->each(
fn ($command) => $pipe->command($command)
)))->run(output: $output)
: (new Pipe($this, $callback))->run(output: $output);
}








public function concurrently(callable $callback, ?callable $output = null)
{
return (new Pool($this, $callback))->start($output)->wait();
}






public function newPendingProcess()
{
return (new PendingProcess($this))->withFakeHandlers($this->fakeHandlers);
}








public function __call($method, $parameters)
{
if (static::hasMacro($method)) {
return $this->macroCall($method, $parameters);
}

return $this->newPendingProcess()->{$method}(...$parameters);
}
}
