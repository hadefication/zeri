<?php










declare(strict_types=1);

namespace Fidry\CpuCoreCounter\Finder;

use function preg_match;






final class CmiCmdletPhysicalFinder extends ProcOpenBasedFinder
{
private const CPU_CORE_COUNT_REGEX = '/NumberOfCores[\s\n]-+[\s\n]+(?<count>\d+)/';

protected function getCommand(): string
{
return 'Get-CimInstance -ClassName Win32_Processor | Select-Object -Property NumberOfCores';
}

public function toString(): string
{
return 'CmiCmdletPhysicalFinder';
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
