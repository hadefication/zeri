<?php

declare(strict_types=1);

namespace Pest\Plugins;

use Pest\Contracts\Plugins\HandlesArguments;




final class Verbose implements HandlesArguments
{
use Concerns\HandleArguments;




private const VERBOSITY_LEVELS = ['v', 'vv', 'vvv', 'q'];




public function handleArguments(array $arguments): array
{
foreach (self::VERBOSITY_LEVELS as $level) {
if ($this->hasArgument('-'.$level, $arguments)) {
$arguments = $this->popArgument('-'.$level, $arguments);
}
}

if ($this->hasArgument('--quiet', $arguments)) {
return $this->popArgument('--quiet', $arguments);
}

return $arguments;
}
}
