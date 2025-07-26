<?php

namespace Illuminate\Process;

use Illuminate\Contracts\Process\ProcessResult as ProcessResultContract;
use Illuminate\Process\Exceptions\ProcessFailedException;
use Illuminate\Support\Collection;

class FakeProcessResult implements ProcessResultContract
{





protected $command;






protected $exitCode;






protected $output = '';






protected $errorOutput = '';









public function __construct(string $command = '', int $exitCode = 0, array|string $output = '', array|string $errorOutput = '')
{
$this->command = $command;
$this->exitCode = $exitCode;
$this->output = $this->normalizeOutput($output);
$this->errorOutput = $this->normalizeOutput($errorOutput);
}







protected function normalizeOutput(array|string $output)
{
if (empty($output)) {
return '';
} elseif (is_string($output)) {
return rtrim($output, "\n")."\n";
} elseif (is_array($output)) {
return rtrim(
(new Collection($output))
->map(fn ($line) => rtrim($line, "\n")."\n")
->implode(''),
"\n"
);
}
}






public function command()
{
return $this->command;
}







public function withCommand(string $command)
{
return new FakeProcessResult($command, $this->exitCode, $this->output, $this->errorOutput);
}






public function successful()
{
return $this->exitCode === 0;
}






public function failed()
{
return ! $this->successful();
}






public function exitCode()
{
return $this->exitCode;
}






public function output()
{
return $this->output;
}







public function seeInOutput(string $output)
{
return str_contains($this->output(), $output);
}






public function errorOutput()
{
return $this->errorOutput;
}







public function seeInErrorOutput(string $output)
{
return str_contains($this->errorOutput(), $output);
}









public function throw(?callable $callback = null)
{
if ($this->successful()) {
return $this;
}

$exception = new ProcessFailedException($this);

if ($callback) {
$callback($this, $exception);
}

throw $exception;
}










public function throwIf(bool $condition, ?callable $callback = null)
{
if ($condition) {
return $this->throw($callback);
}

return $this;
}
}
