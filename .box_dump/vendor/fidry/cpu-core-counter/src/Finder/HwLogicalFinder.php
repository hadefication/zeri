<?php










declare(strict_types=1);

namespace Fidry\CpuCoreCounter\Finder;







final class HwLogicalFinder extends ProcOpenBasedFinder
{
protected function getCommand(): string
{
return 'sysctl -n hw.logicalcpu';
}

public function toString(): string
{
return 'HwLogicalFinder';
}
}
