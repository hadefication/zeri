<?php










declare(strict_types=1);

namespace Fidry\CpuCoreCounter\Finder;

use function file_get_contents;
use function is_file;
use function sprintf;
use function substr_count;
use const PHP_EOL;








final class CpuInfoFinder implements CpuCoreFinder
{
private const CPU_INFO_PATH = '/proc/cpuinfo';

public function diagnose(): string
{
if (!is_file(self::CPU_INFO_PATH)) {
return sprintf(
'The file "%s" could not be found.',
self::CPU_INFO_PATH
);
}

$cpuInfo = file_get_contents(self::CPU_INFO_PATH);

if (false === $cpuInfo) {
return sprintf(
'Could not get the content of the file "%s".',
self::CPU_INFO_PATH
);
}

return sprintf(
'Found the file "%s" with the content:%s%s%sWill return "%s".',
self::CPU_INFO_PATH,
PHP_EOL,
$cpuInfo,
PHP_EOL,
self::countCpuCores($cpuInfo)
);
}




public function find(): ?int
{
$cpuInfo = self::getCpuInfo();

return null === $cpuInfo ? null : self::countCpuCores($cpuInfo);
}

public function toString(): string
{
return 'CpuInfoFinder';
}

private static function getCpuInfo(): ?string
{
if (!@is_file(self::CPU_INFO_PATH)) {
return null;
}

$cpuInfo = @file_get_contents(self::CPU_INFO_PATH);

return false === $cpuInfo
? null
: $cpuInfo;
}






public static function countCpuCores(string $cpuInfo): ?int
{
$processorCount = substr_count($cpuInfo, 'processor');

return $processorCount > 0 ? $processorCount : null;
}
}
