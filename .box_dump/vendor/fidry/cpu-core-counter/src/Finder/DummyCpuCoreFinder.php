<?php










declare(strict_types=1);

namespace Fidry\CpuCoreCounter\Finder;

use function sprintf;





final class DummyCpuCoreFinder implements CpuCoreFinder
{



private $count;

public function diagnose(): string
{
return sprintf(
'Will return "%d".',
$this->count
);
}




public function __construct(int $count)
{
$this->count = $count;
}

public function find(): ?int
{
return $this->count;
}

public function toString(): string
{
return sprintf(
'DummyCpuCoreFinder(value=%d)',
$this->count
);
}
}
