<?php

namespace Illuminate\Contracts\Process;

interface InvokedProcess
{





public function id();







public function signal(int $signal);






public function running();






public function output();






public function errorOutput();






public function latestOutput();






public function latestErrorOutput();







public function wait(?callable $output = null);
}
