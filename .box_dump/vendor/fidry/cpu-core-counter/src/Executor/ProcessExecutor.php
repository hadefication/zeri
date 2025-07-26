<?php










declare(strict_types=1);

namespace Fidry\CpuCoreCounter\Executor;

interface ProcessExecutor
{



public function execute(string $command): ?array;
}
