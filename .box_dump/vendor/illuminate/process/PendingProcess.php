<?php

namespace Illuminate\Process;

use Closure;
use Illuminate\Process\Exceptions\ProcessTimedOutException;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Conditionable;
use LogicException;
use RuntimeException;
use Symfony\Component\Process\Exception\ProcessTimedOutException as SymfonyTimeoutException;
use Symfony\Component\Process\Process;

class PendingProcess
{
use Conditionable;






protected $factory;






public $command;






public $path;






public $timeout = 60;






public $idleTimeout;






public $environment = [];






public $input;






public $quietly = false;






public $tty = false;






public $options = [];






protected $fakeHandlers = [];






public function __construct(Factory $factory)
{
$this->factory = $factory;
}







public function command(array|string $command)
{
$this->command = $command;

return $this;
}







public function path(string $path)
{
$this->path = $path;

return $this;
}







public function timeout(int $timeout)
{
$this->timeout = $timeout;

return $this;
}







public function idleTimeout(int $timeout)
{
$this->idleTimeout = $timeout;

return $this;
}






public function forever()
{
$this->timeout = null;

return $this;
}







public function env(array $environment)
{
$this->environment = $environment;

return $this;
}







public function input($input)
{
$this->input = $input;

return $this;
}






public function quietly()
{
$this->quietly = true;

return $this;
}







public function tty(bool $tty = true)
{
$this->tty = $tty;

return $this;
}







public function options(array $options)
{
$this->options = $options;

return $this;
}











public function run(array|string|null $command = null, ?callable $output = null)
{
$this->command = $command ?: $this->command;

$process = $this->toSymfonyProcess($command);
try {
if ($fake = $this->fakeFor($command = $process->getCommandline())) {
return tap($this->resolveSynchronousFake($command, $fake), function ($result) {
$this->factory->recordIfRecording($this, $result);
});
} elseif ($this->factory->isRecording() && $this->factory->preventingStrayProcesses()) {
throw new RuntimeException('Attempted process ['.$command.'] without a matching fake.');
}

return new ProcessResult(tap($process)->run($output));
} catch (SymfonyTimeoutException $e) {
throw new ProcessTimedOutException($e, new ProcessResult($process));
}
}










public function start(array|string|null $command = null, ?callable $output = null)
{
$this->command = $command ?: $this->command;

$process = $this->toSymfonyProcess($command);

if ($fake = $this->fakeFor($command = $process->getCommandline())) {
return tap($this->resolveAsynchronousFake($command, $output, $fake), function (FakeInvokedProcess $process) {
$this->factory->recordIfRecording($this, $process->predictProcessResult());
});
} elseif ($this->factory->isRecording() && $this->factory->preventingStrayProcesses()) {
throw new RuntimeException('Attempted process ['.$command.'] without a matching fake.');
}

return new InvokedProcess(tap($process)->start($output));
}







protected function toSymfonyProcess(array|string|null $command)
{
$command = $command ?? $this->command;

$process = is_iterable($command)
? new Process($command, null, $this->environment)
: Process::fromShellCommandline((string) $command, null, $this->environment);

$process->setWorkingDirectory((string) ($this->path ?? getcwd()));
$process->setTimeout($this->timeout);

if ($this->idleTimeout) {
$process->setIdleTimeout($this->idleTimeout);
}

if ($this->input) {
$process->setInput($this->input);
}

if ($this->quietly) {
$process->disableOutput();
}

if ($this->tty) {
$process->setTty(true);
}

if (! empty($this->options)) {
$process->setOptions($this->options);
}

return $process;
}






public function supportsTty()
{
return Process::isTtySupported();
}







public function withFakeHandlers(array $fakeHandlers)
{
$this->fakeHandlers = $fakeHandlers;

return $this;
}







protected function fakeFor(string $command)
{
return (new Collection($this->fakeHandlers))
->first(fn ($handler, $pattern) => $pattern === '*' || Str::is($pattern, $command));
}








protected function resolveSynchronousFake(string $command, Closure $fake)
{
$result = $fake($this);

if (is_int($result)) {
return (new FakeProcessResult(exitCode: $result))->withCommand($command);
}

if (is_string($result) || is_array($result)) {
return (new FakeProcessResult(output: $result))->withCommand($command);
}

return match (true) {
$result instanceof ProcessResult => $result,
$result instanceof FakeProcessResult => $result->withCommand($command),
$result instanceof FakeProcessDescription => $result->toProcessResult($command),
$result instanceof FakeProcessSequence => $this->resolveSynchronousFake($command, fn () => $result()),
$result instanceof \Throwable => throw $result,
default => throw new LogicException('Unsupported synchronous process fake result provided.'),
};
}











protected function resolveAsynchronousFake(string $command, ?callable $output, Closure $fake)
{
$result = $fake($this);

if (is_string($result) || is_array($result)) {
$result = new FakeProcessResult(output: $result);
}

if ($result instanceof ProcessResult) {
return (new FakeInvokedProcess(
$command,
(new FakeProcessDescription)
->replaceOutput($result->output())
->replaceErrorOutput($result->errorOutput())
->runsFor(iterations: 0)
->exitCode($result->exitCode())
))->withOutputHandler($output);
} elseif ($result instanceof FakeProcessResult) {
return (new FakeInvokedProcess(
$command,
(new FakeProcessDescription)
->replaceOutput($result->output())
->replaceErrorOutput($result->errorOutput())
->runsFor(iterations: 0)
->exitCode($result->exitCode())
))->withOutputHandler($output);
} elseif ($result instanceof FakeProcessDescription) {
return (new FakeInvokedProcess($command, $result))->withOutputHandler($output);
} elseif ($result instanceof FakeProcessSequence) {
return $this->resolveAsynchronousFake($command, $output, fn () => $result());
}

throw new LogicException('Unsupported asynchronous process fake result provided.');
}
}
