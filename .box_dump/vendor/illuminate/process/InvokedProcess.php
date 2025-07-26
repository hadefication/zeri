<?php

namespace Illuminate\Process;

use Illuminate\Contracts\Process\InvokedProcess as InvokedProcessContract;
use Illuminate\Process\Exceptions\ProcessTimedOutException;
use Symfony\Component\Process\Exception\ProcessTimedOutException as SymfonyTimeoutException;
use Symfony\Component\Process\Process;

class InvokedProcess implements InvokedProcessContract
{





protected $process;






public function __construct(Process $process)
{
$this->process = $process;
}






public function id()
{
return $this->process->getPid();
}







public function signal(int $signal)
{
$this->process->signal($signal);

return $this;
}








public function stop(float $timeout = 10, ?int $signal = null)
{
return $this->process->stop($timeout, $signal);
}






public function running()
{
return $this->process->isRunning();
}






public function output()
{
return $this->process->getOutput();
}






public function errorOutput()
{
return $this->process->getErrorOutput();
}






public function latestOutput()
{
return $this->process->getIncrementalOutput();
}






public function latestErrorOutput()
{
return $this->process->getIncrementalErrorOutput();
}








public function ensureNotTimedOut()
{
try {
$this->process->checkTimeout();
} catch (SymfonyTimeoutException $e) {
throw new ProcessTimedOutException($e, new ProcessResult($this->process));
}
}









public function wait(?callable $output = null)
{
try {
$this->process->wait($output);

return new ProcessResult($this->process);
} catch (SymfonyTimeoutException $e) {
throw new ProcessTimedOutException($e, new ProcessResult($this->process));
}
}









public function waitUntil(?callable $output = null)
{
try {
$this->process->waitUntil($output);

return new ProcessResult($this->process);
} catch (SymfonyTimeoutException $e) {
throw new ProcessTimedOutException($e, new ProcessResult($this->process));
}
}
}
