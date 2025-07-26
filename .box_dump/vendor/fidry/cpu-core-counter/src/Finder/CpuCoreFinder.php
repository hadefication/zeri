<?php










declare(strict_types=1);

namespace Fidry\CpuCoreCounter\Finder;

interface CpuCoreFinder
{








public function diagnose(): string;







public function find(): ?int;

public function toString(): string;
}
