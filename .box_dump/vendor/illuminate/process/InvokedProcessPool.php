<?php

namespace Illuminate\Process;

use Countable;
use Illuminate\Support\Collection;

class InvokedProcessPool implements Countable
{





protected $invokedProcesses;






public function __construct(array $invokedProcesses)
{
$this->invokedProcesses = $invokedProcesses;
}







public function signal(int $signal)
{
return $this->running()->each->signal($signal);
}








public function stop(float $timeout = 10, ?int $signal = null)
{
return $this->running()->each->stop($timeout, $signal);
}






public function running()
{
return (new Collection($this->invokedProcesses))->filter->running()->values();
}






public function wait()
{
return new ProcessPoolResults((new Collection($this->invokedProcesses))->map->wait()->all());
}






public function count(): int
{
return count($this->invokedProcesses);
}
}
