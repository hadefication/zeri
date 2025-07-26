<?php










declare(strict_types=1);

namespace Fidry\CpuCoreCounter\Finder;

use function preg_match;





final class CmiCmdletLogicalFinder extends ProcOpenBasedFinder
{
private const CPU_CORE_COUNT_REGEX = '/NumberOfLogicalProcessors[\s\n]-+[\s\n]+(?<count>\d+)/';

protected function getCommand(): string
{
return 'Get-CimInstance -ClassName Win32_ComputerSystem | Select-Object -Property NumberOfLogicalProcessors';
}

public function toString(): string
{
return 'CmiCmdletLogicalFinder';
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
