<?php declare(strict_types=1);








namespace SebastianBergmann\CodeCoverage\Driver;

use SebastianBergmann\CodeCoverage\Filter;
use SebastianBergmann\CodeCoverage\NoCodeCoverageDriverAvailableException;
use SebastianBergmann\CodeCoverage\NoCodeCoverageDriverWithPathCoverageSupportAvailableException;
use SebastianBergmann\Environment\Runtime;

final class Selector
{






public function forLineCoverage(Filter $filter): Driver
{
$runtime = new Runtime;

if ($runtime->hasPCOV()) {
return new PcovDriver($filter);
}

if ($runtime->hasXdebug()) {
$driver = new XdebugDriver($filter);

$driver->enableDeadCodeDetection();

return $driver;
}

throw new NoCodeCoverageDriverAvailableException;
}






public function forLineAndPathCoverage(Filter $filter): Driver
{
if ((new Runtime)->hasXdebug()) {
$driver = new XdebugDriver($filter);

$driver->enableDeadCodeDetection();
$driver->enableBranchAndPathCoverage();

return $driver;
}

throw new NoCodeCoverageDriverWithPathCoverageSupportAvailableException;
}
}
