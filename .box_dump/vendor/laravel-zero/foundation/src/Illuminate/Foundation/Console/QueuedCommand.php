<?php

namespace Illuminate\Foundation\Console;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Console\Kernel as KernelContract;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class QueuedCommand implements ShouldQueue
{
use Dispatchable, Queueable;






protected $data;






public function __construct($data)
{
$this->data = $data;
}







public function handle(KernelContract $kernel)
{
$kernel->call(...array_values($this->data));
}






public function displayName()
{
return array_values($this->data)[0];
}
}
