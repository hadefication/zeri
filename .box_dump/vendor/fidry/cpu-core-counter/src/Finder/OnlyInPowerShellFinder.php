<?php










declare(strict_types=1);

namespace Fidry\CpuCoreCounter\Finder;

use function getenv;
use function sprintf;

final class OnlyInPowerShellFinder implements CpuCoreFinder
{



private $decoratedFinder;

public function __construct(CpuCoreFinder $decoratedFinder)
{
$this->decoratedFinder = $decoratedFinder;
}

public function diagnose(): string
{
$powerShellModulePath = getenv('PSModulePath');

return $this->skip()
? sprintf(
'Skipped; no power shell module path detected ("%s").',
$powerShellModulePath
)
: $this->decoratedFinder->diagnose();
}

public function find(): ?int
{
return $this->skip()
? null
: $this->decoratedFinder->find();
}

public function toString(): string
{
return sprintf(
'OnlyInPowerShellFinder(%s)',
$this->decoratedFinder->toString()
);
}

private function skip(): bool
{
return false === getenv('PSModulePath');
}
}
