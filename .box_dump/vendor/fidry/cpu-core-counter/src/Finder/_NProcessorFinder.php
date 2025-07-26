<?php










declare(strict_types=1);

namespace Fidry\CpuCoreCounter\Finder;






final class _NProcessorFinder extends ProcOpenBasedFinder
{
protected function getCommand(): string
{
return 'getconf _NPROCESSORS_ONLN';
}

public function toString(): string
{
return '_NProcessorFinder';
}
}
