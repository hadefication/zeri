<?php

declare(strict_types=1);

namespace Pest\Plugins;

use Pest\Contracts\Plugins\HandlesArguments;
use Pest\Plugins\Concerns\HandleArguments;




final class Bail implements HandlesArguments
{
use HandleArguments;




public function handleArguments(array $arguments): array
{
if ($this->hasArgument('--bail', $arguments)) {
$arguments = $this->popArgument('--bail', $arguments);

$arguments = $this->pushArgument('--stop-on-failure', $arguments);
$arguments = $this->pushArgument('--stop-on-error', $arguments);
}

return $arguments;
}
}
