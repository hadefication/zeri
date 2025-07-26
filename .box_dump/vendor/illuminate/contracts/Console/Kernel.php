<?php

namespace Illuminate\Contracts\Console;

interface Kernel
{





public function bootstrap();








public function handle($input, $output = null);









public function call($command, array $parameters = [], $outputBuffer = null);








public function queue($command, array $parameters = []);






public function all();






public function output();








public function terminate($input, $status);
}
