<?php










declare(strict_types=1);

namespace Fidry\CpuCoreCounter\Finder;






final class NProcessorFinder extends ProcOpenBasedFinder
{
protected function getCommand(): string
{
return 'getconf NPROCESSORS_ONLN';
}

public function toString(): string
{
return 'NProcessorFinder';
}
}
