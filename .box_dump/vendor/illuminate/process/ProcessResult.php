<?php

namespace Illuminate\Process;

use Illuminate\Contracts\Process\ProcessResult as ProcessResultContract;
use Illuminate\Process\Exceptions\ProcessFailedException;
use Symfony\Component\Process\Process;

class ProcessResult implements ProcessResultContract
{





protected $process;






public function __construct(Process $process)
{
$this->process = $process;
}






public function command()
{
return $this->process->getCommandLine();
}






public function successful()
{
return $this->process->isSuccessful();
}






public function failed()
{
return ! $this->successful();
}






public function exitCode()
{
return $this->process->getExitCode();
}






public function output()
{
return $this->process->getOutput();
}







public function seeInOutput(string $output)
{
return str_contains($this->output(), $output);
}






public function errorOutput()
{
return $this->process->getErrorOutput();
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
