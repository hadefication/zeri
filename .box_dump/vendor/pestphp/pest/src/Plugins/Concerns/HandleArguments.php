<?php

declare(strict_types=1);

namespace Pest\Plugins\Concerns;




trait HandleArguments
{





public function hasArgument(string $argument, array $arguments): bool
{
foreach ($arguments as $arg) {
if ($arg === $argument) {
return true;
}

if (str_starts_with((string) $arg, "$argument=")) { 
return true;
}
}

return false;
}







public function pushArgument(string $argument, array $arguments): array
{
$arguments[] = $argument;

return $arguments;
}







public function popArgument(string $argument, array $arguments): array
{
$arguments = array_flip($arguments);

unset($arguments[$argument]);

return array_values(array_flip($arguments));
}
}
