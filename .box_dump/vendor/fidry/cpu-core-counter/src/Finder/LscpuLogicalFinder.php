<?php










declare(strict_types=1);

namespace Fidry\CpuCoreCounter\Finder;

use function count;
use function explode;
use function is_array;
use function preg_grep;
use const PHP_EOL;






final class LscpuLogicalFinder extends ProcOpenBasedFinder
{
public function getCommand(): string
{
return 'lscpu -p';
}

protected function countCpuCores(string $process): ?int
{
$lines = explode(PHP_EOL, $process);
$actualLines = preg_grep('/^\d+,/', $lines);

if (!is_array($actualLines)) {
return null;
}

$count = count($actualLines);

return 0 === $count ? null : $count;
}

public function toString(): string
{
return 'LscpuLogicalFinder';
}
}
