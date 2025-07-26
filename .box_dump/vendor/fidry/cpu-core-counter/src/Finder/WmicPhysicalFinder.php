<?php










declare(strict_types=1);

namespace Fidry\CpuCoreCounter\Finder;

use function preg_match;






final class WmicPhysicalFinder extends ProcOpenBasedFinder
{
private const CPU_CORE_COUNT_REGEX = '/NumberOfCores[\s\n]+(?<count>\d+)/';

protected function getCommand(): string
{
return 'wmic cpu get NumberOfCores';
}

public function toString(): string
{
return 'WmicPhysicalFinder';
}

protected function countCpuCores(string $process): ?int
{
if (0 === preg_match(self::CPU_CORE_COUNT_REGEX, $process, $matches)) {
return parent::countCpuCores($process);
}

$count = $matches['count'];

return parent::countCpuCores($count);
}
}
