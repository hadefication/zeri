<?php

declare(strict_types=1);

namespace Pest\Plugins\Parallel\Handlers;

use Pest\Contracts\Plugins\HandlesArguments;
use Pest\Plugins\Concerns\HandleArguments;
use Pest\Plugins\Parallel\Paratest\WrapperRunner;




final class Parallel implements HandlesArguments
{
use HandleArguments;




private const ARGS_TO_REMOVE = [
'--parallel',
'-p',
'--no-output',
'--cache-result',
];




public function handleArguments(array $arguments): array
{
$args = array_reduce(self::ARGS_TO_REMOVE, fn (array $args, string $arg): array => $this->popArgument($arg, $args), $arguments);

return $this->pushArgument('--runner='.WrapperRunner::class, $args);
}
}
