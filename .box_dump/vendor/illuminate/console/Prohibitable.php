<?php

namespace Illuminate\Console;

trait Prohibitable
{





protected static $prohibitedFromRunning = false;







public static function prohibit($prohibit = true)
{
static::$prohibitedFromRunning = $prohibit;
}







protected function isProhibited(bool $quiet = false)
{
if (! static::$prohibitedFromRunning) {
return false;
}

if (! $quiet) {
$this->components->warn('This command is prohibited from running in this environment.');
}

return true;
}
}
