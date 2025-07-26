<?php

namespace Illuminate\Process;

use Illuminate\Contracts\Process\InvokedProcess as InvokedProcessContract;

class FakeInvokedProcess implements InvokedProcessContract
{





protected $command;






protected $process;






protected $receivedSignals = [];






protected $remainingRunIterations;






protected $outputHandler;






protected $nextOutputIndex = 0;






protected $nextErrorOutputIndex = 0;







public function __construct(string $command, FakeProcessDescription $process)
{
$this->command = $command;
$this->process = $process;
}






public function id()
{
$this->invokeOutputHandlerWithNextLineOfOutput();

return $this->process->processId;
}







public function signal(int $signal)
{
$this->invokeOutputHandlerWithNextLineOfOutput();

$this->receivedSignals[] = $signal;

return $this;
}







public function hasReceivedSignal(int $signal)
{
return in_array($signal, $this->receivedSignals);
}






public function running()
{
$this->invokeOutputHandlerWithNextLineOfOutput();

$this->remainingRunIterations = is_null($this->remainingRunIterations)
? $this->process->runIterations
: $this->remainingRunIterations;

if ($this->remainingRunIterations === 0) {
while ($this->invokeOutputHandlerWithNextLineOfOutput()) {
}

return false;
}

$this->remainingRunIterations = $this->remainingRunIterations - 1;

return true;
}






protected function invokeOutputHandlerWithNextLineOfOutput()
{
if (! $this->outputHandler) {
return false;
}

[$outputCount, $outputStartingPoint] = [
count($this->process->output),
min($this->nextOutputIndex, $this->nextErrorOutputIndex),
];

for ($i = $outputStartingPoint; $i < $outputCount; $i++) {
$currentOutput = $this->process->output[$i];

if ($currentOutput['type'] === 'out' && $i >= $this->nextOutputIndex) {
call_user_func($this->outputHandler, 'out', $currentOutput['buffer']);
$this->nextOutputIndex = $i + 1;

return $currentOutput;
} elseif ($currentOutput['type'] === 'err' && $i >= $this->nextErrorOutputIndex) {
call_user_func($this->outputHandler, 'err', $currentOutput['buffer']);
$this->nextErrorOutputIndex = $i + 1;

return $currentOutput;
}
}

return false;
}






public function output()
{
$this->latestOutput();

$output = [];

for ($i = 0; $i < $this->nextOutputIndex; $i++) {
if ($this->process->output[$i]['type'] === 'out') {
$output[] = $this->process->output[$i]['buffer'];
}
}

return rtrim(implode('', $output), "\n")."\n";
}






public function errorOutput()
{
$this->latestErrorOutput();

$output = [];

for ($i = 0; $i < $this->nextErrorOutputIndex; $i++) {
if ($this->process->output[$i]['type'] === 'err') {
$output[] = $this->process->output[$i]['buffer'];
}
}

return rtrim(implode('', $output), "\n")."\n";
}






public function latestOutput()
{
$outputCount = count($this->process->output);

for ($i = $this->nextOutputIndex; $i < $outputCount; $i++) {
if ($this->process->output[$i]['type'] === 'out') {
$output = $this->process->output[$i]['buffer'];
$this->nextOutputIndex = $i + 1;

break;
}

$this->nextOutputIndex = $i + 1;
}

return $output ?? '';
}






public function latestErrorOutput()
{
$outputCount = count($this->process->output);

for ($i = $this->nextErrorOutputIndex; $i < $outputCount; $i++) {
if ($this->process->output[$i]['type'] === 'err') {
$output = $this->process->output[$i]['buffer'];
$this->nextErrorOutputIndex = $i + 1;

break;
}

$this->nextErrorOutputIndex = $i + 1;
}

return $output ?? '';
}







public function wait(?callable $output = null)
{
$this->outputHandler = $output ?: $this->outputHandler;

if (! $this->outputHandler) {
$this->remainingRunIterations = 0;

return $this->predictProcessResult();
}

while ($this->invokeOutputHandlerWithNextLineOfOutput()) {

}

$this->remainingRunIterations = 0;

return $this->process->toProcessResult($this->command);
}






public function predictProcessResult()
{
return $this->process->toProcessResult($this->command);
}







public function withOutputHandler(?callable $outputHandler)
{
$this->outputHandler = $outputHandler;

return $this;
}
}
