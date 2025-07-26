<?php declare(strict_types=1);








namespace SebastianBergmann\CodeCoverage\Driver;

use function sprintf;
use SebastianBergmann\CodeCoverage\BranchAndPathCoverageNotSupportedException;
use SebastianBergmann\CodeCoverage\Data\RawCodeCoverageData;
use SebastianBergmann\CodeCoverage\DeadCodeDetectionNotSupportedException;




abstract class Driver
{





public const LINE_NOT_EXECUTABLE = -2;






public const LINE_NOT_EXECUTED = -1;






public const LINE_EXECUTED = 1;






public const BRANCH_NOT_HIT = 0;






public const BRANCH_HIT = 1;
private bool $collectBranchAndPathCoverage = false;
private bool $detectDeadCode = false;

public function canCollectBranchAndPathCoverage(): bool
{
return false;
}

public function collectsBranchAndPathCoverage(): bool
{
return $this->collectBranchAndPathCoverage;
}




public function enableBranchAndPathCoverage(): void
{
if (!$this->canCollectBranchAndPathCoverage()) {
throw new BranchAndPathCoverageNotSupportedException(
sprintf(
'%s does not support branch and path coverage',
$this->nameAndVersion(),
),
);
}

$this->collectBranchAndPathCoverage = true;
}

public function disableBranchAndPathCoverage(): void
{
$this->collectBranchAndPathCoverage = false;
}

public function canDetectDeadCode(): bool
{
return false;
}

public function detectsDeadCode(): bool
{
return $this->detectDeadCode;
}




public function enableDeadCodeDetection(): void
{
if (!$this->canDetectDeadCode()) {
throw new DeadCodeDetectionNotSupportedException(
sprintf(
'%s does not support dead code detection',
$this->nameAndVersion(),
),
);
}

$this->detectDeadCode = true;
}

public function disableDeadCodeDetection(): void
{
$this->detectDeadCode = false;
}

abstract public function nameAndVersion(): string;

abstract public function start(): void;

abstract public function stop(): RawCodeCoverageData;
}
