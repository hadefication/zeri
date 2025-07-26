<?php










declare(strict_types=1);

namespace Fidry\CpuCoreCounter\Finder;




final class NullCpuCoreFinder implements CpuCoreFinder
{
public function diagnose(): string
{
return 'Will return "null".';
}

public function find(): ?int
{
return null;
}

public function toString(): string
{
return 'NullCpuCoreFinder';
}
}
