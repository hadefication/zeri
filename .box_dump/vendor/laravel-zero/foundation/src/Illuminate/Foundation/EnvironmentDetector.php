<?php

namespace Illuminate\Foundation;

use Closure;

class EnvironmentDetector
{







public function detect(Closure $callback, $consoleArgs = null)
{
if ($consoleArgs) {
return $this->detectConsoleEnvironment($callback, $consoleArgs);
}

return $this->detectWebEnvironment($callback);
}







protected function detectWebEnvironment(Closure $callback)
{
return $callback();
}








protected function detectConsoleEnvironment(Closure $callback, array $args)
{



if (! is_null($value = $this->getEnvironmentArgument($args))) {
return $value;
}

return $this->detectWebEnvironment($callback);
}







protected function getEnvironmentArgument(array $args)
{
foreach ($args as $i => $value) {
if ($value === '--env') {
return $args[$i + 1] ?? null;
}

if (str_starts_with($value, '--env=')) {
return head(array_slice(explode('=', $value), 1));
}
}
}
}
