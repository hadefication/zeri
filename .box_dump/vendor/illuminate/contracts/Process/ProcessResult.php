<?php

namespace Illuminate\Contracts\Process;

interface ProcessResult
{





public function command();






public function successful();






public function failed();






public function exitCode();






public function output();







public function seeInOutput(string $output);






public function errorOutput();







public function seeInErrorOutput(string $output);







public function throw(?callable $callback = null);








public function throwIf(bool $condition, ?callable $callback = null);
}
