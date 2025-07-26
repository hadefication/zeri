<?php










declare(strict_types=1);

namespace Fidry\CpuCoreCounter\Finder;

use function array_filter;
use function count;
use function explode;
use const PHP_EOL;






final class WindowsRegistryLogicalFinder extends ProcOpenBasedFinder
{
protected function getCommand(): string
{
return 'reg query HKEY_LOCAL_MACHINE\HARDWARE\DESCRIPTION\System\CentralProcessor';
}

public function toString(): string
{
return 'WindowsRegistryLogicalFinder';
}

protected function countCpuCores(string $process): ?int
{
$count = count(
array_filter(
explode(PHP_EOL, $process),
static function (string $line): bool {
return '' !== trim($line);
}
)
);

return $count > 0 ? $count : null;
}
}
