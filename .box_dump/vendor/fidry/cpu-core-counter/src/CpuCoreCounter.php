<?php










declare(strict_types=1);

namespace Fidry\CpuCoreCounter;

use Fidry\CpuCoreCounter\Finder\CpuCoreFinder;
use Fidry\CpuCoreCounter\Finder\EnvVariableFinder;
use Fidry\CpuCoreCounter\Finder\FinderRegistry;
use InvalidArgumentException;
use function implode;
use function max;
use function sprintf;
use function sys_getloadavg;
use const PHP_EOL;

final class CpuCoreCounter
{



private $finders;




private $count;




public function __construct(?array $finders = null)
{
$this->finders = $finders ?? FinderRegistry::getDefaultLogicalFinders();
}



































public function getAvailableForParallelisation(
int $reservedCpus = 0,
?int $countLimit = null,
?float $loadLimit = null,
?float $systemLoadAverage = 0.
): ParallelisationResult {
self::checkCountLimit($countLimit);
self::checkLoadLimit($loadLimit);
self::checkSystemLoadAverage($systemLoadAverage);

$totalCoreCount = $this->getCountWithFallback(1);
$availableCores = max(1, $totalCoreCount - $reservedCpus);


if (null !== $loadLimit) {
$correctedSystemLoadAverage = null === $systemLoadAverage
? sys_getloadavg()[0] ?? 0.
: $systemLoadAverage;

$availableCores = max(
1,
$loadLimit * ($availableCores - $correctedSystemLoadAverage)
);
}

if (null === $countLimit) {
$correctedCountLimit = self::getKubernetesLimit();
} else {
$correctedCountLimit = $countLimit > 0
? $countLimit
: max(1, $totalCoreCount + $countLimit);
}

if (null !== $correctedCountLimit && $availableCores > $correctedCountLimit) {
$availableCores = $correctedCountLimit;
}

return new ParallelisationResult(
$reservedCpus,
$countLimit,
$loadLimit,
$systemLoadAverage,
$correctedCountLimit,
$correctedSystemLoadAverage ?? $systemLoadAverage,
$totalCoreCount,
(int) $availableCores
);
}






public function getCount(): int
{

if (null === $this->count) {
$this->count = $this->findCount();
}

return $this->count;
}






public function getCountWithFallback(int $fallback): int
{
try {
return $this->getCount();
} catch (NumberOfCpuCoreNotFound $exception) {
return $fallback;
}
}




public function trace(): string
{
$output = [];

foreach ($this->finders as $finder) {
$output[] = sprintf(
'Executing the finder "%s":',
$finder->toString()
);
$output[] = $finder->diagnose();

$cores = $finder->find();

if (null !== $cores) {
$output[] = 'Result found: '.$cores;

break;
}

$output[] = '–––';
}

return implode(PHP_EOL, $output);
}






private function findCount(): int
{
foreach ($this->finders as $finder) {
$cores = $finder->find();

if (null !== $cores) {
return $cores;
}
}

throw NumberOfCpuCoreNotFound::create();
}






public function getFinderAndCores(): array
{
foreach ($this->finders as $finder) {
$cores = $finder->find();

if (null !== $cores) {
return [$finder, $cores];
}
}

throw NumberOfCpuCoreNotFound::create();
}




public static function getKubernetesLimit(): ?int
{
$finder = new EnvVariableFinder('KUBERNETES_CPU_LIMIT');

return $finder->find();
}

private static function checkCountLimit(?int $countLimit): void
{
if (0 === $countLimit) {
throw new InvalidArgumentException(
'The count limit must be a non zero integer. Got "0".'
);
}
}

private static function checkLoadLimit(?float $loadLimit): void
{
if (null === $loadLimit) {
return;
}

if ($loadLimit < 0. || $loadLimit > 1.) {
throw new InvalidArgumentException(
sprintf(
'The load limit must be in the range [0., 1.], got "%s".',
$loadLimit
)
);
}
}

private static function checkSystemLoadAverage(?float $systemLoadAverage): void
{
if (null !== $systemLoadAverage && $systemLoadAverage < 0.) {
throw new InvalidArgumentException(
sprintf(
'The system load average must be a positive float, got "%s".',
$systemLoadAverage
)
);
}
}
}
