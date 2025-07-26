<?php










declare(strict_types=1);

namespace Fidry\CpuCoreCounter\Finder;







final class HwPhysicalFinder extends ProcOpenBasedFinder
{
protected function getCommand(): string
{
return 'sysctl -n hw.physicalcpu';
}

public function toString(): string
{
return 'HwPhysicalFinder';
}
}
