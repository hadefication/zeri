<?php declare(strict_types=1);








namespace SebastianBergmann\CodeCoverage\Driver;

use const pcov\inclusive;
use function array_intersect;
use function extension_loaded;
use function pcov\clear;
use function pcov\collect;
use function pcov\start;
use function pcov\stop;
use function pcov\waiting;
use function phpversion;
use SebastianBergmann\CodeCoverage\Data\RawCodeCoverageData;
use SebastianBergmann\CodeCoverage\Filter;




final class PcovDriver extends Driver
{
private readonly Filter $filter;




public function __construct(Filter $filter)
{
$this->ensurePcovIsAvailable();

$this->filter = $filter;
}

public function start(): void
{
start();
}

public function stop(): RawCodeCoverageData
{
stop();

$filesToCollectCoverageFor = waiting();
$collected = [];

if ($filesToCollectCoverageFor) {
if (!$this->filter->isEmpty()) {
$filesToCollectCoverageFor = array_intersect($filesToCollectCoverageFor, $this->filter->files());
}

$collected = collect(inclusive, $filesToCollectCoverageFor);

clear();
}

return RawCodeCoverageData::fromXdebugWithoutPathCoverage($collected);
}

public function nameAndVersion(): string
{
return 'PCOV ' . phpversion('pcov');
}




private function ensurePcovIsAvailable(): void
{
if (!extension_loaded('pcov')) {
throw new PcovNotAvailableException;
}
}
}
