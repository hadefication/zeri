<?php

namespace Illuminate\Process;

use Illuminate\Contracts\Process\ProcessResult as ProcessResultContract;
use OutOfBoundsException;

class FakeProcessSequence
{





protected $processes = [];






protected $failWhenEmpty = true;






protected $emptyProcess;






public function __construct(array $processes = [])
{
$this->processes = $processes;
}







public function push(ProcessResultContract|FakeProcessDescription|array|string $process)
{
$this->processes[] = $this->toProcessResult($process);

return $this;
}







public function whenEmpty(ProcessResultContract|FakeProcessDescription|array|string $process)
{
$this->failWhenEmpty = false;
$this->emptyProcess = $this->toProcessResult($process);

return $this;
}







protected function toProcessResult(ProcessResultContract|FakeProcessDescription|array|string $process)
{
return is_array($process) || is_string($process)
? new FakeProcessResult(output: $process)
: $process;
}






public function dontFailWhenEmpty()
{
return $this->whenEmpty(new FakeProcessResult);
}






public function isEmpty()
{
return count($this->processes) === 0;
}








public function __invoke()
{
if ($this->failWhenEmpty && count($this->processes) === 0) {
throw new OutOfBoundsException('A process was invoked, but the process result sequence is empty.');
}

if (! $this->failWhenEmpty && count($this->processes) === 0) {
return value($this->emptyProcess ?? new FakeProcessResult);
}

return array_shift($this->processes);
}
}
