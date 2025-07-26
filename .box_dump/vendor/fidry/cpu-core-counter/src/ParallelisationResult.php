<?php










declare(strict_types=1);

namespace Fidry\CpuCoreCounter;

/**
@readonly
*/
final class ParallelisationResult
{



public $passedReservedCpus;




public $passedCountLimit;




public $passedLoadLimit;




public $passedSystemLoadAverage;




public $correctedCountLimit;




public $correctedSystemLoadAverage;




public $totalCoresCount;




public $availableCpus;








public function __construct(
int $passedReservedCpus,
?int $passedCountLimit,
?float $passedLoadLimit,
?float $passedSystemLoadAverage,
?int $correctedCountLimit,
?float $correctedSystemLoadAverage,
int $totalCoresCount,
int $availableCpus
) {
$this->passedReservedCpus = $passedReservedCpus;
$this->passedCountLimit = $passedCountLimit;
$this->passedLoadLimit = $passedLoadLimit;
$this->passedSystemLoadAverage = $passedSystemLoadAverage;
$this->correctedCountLimit = $correctedCountLimit;
$this->correctedSystemLoadAverage = $correctedSystemLoadAverage;
$this->totalCoresCount = $totalCoresCount;
$this->availableCpus = $availableCpus;
}
}
