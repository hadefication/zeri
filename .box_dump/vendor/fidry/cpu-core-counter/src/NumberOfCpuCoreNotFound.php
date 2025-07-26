<?php










declare(strict_types=1);

namespace Fidry\CpuCoreCounter;

use RuntimeException;

final class NumberOfCpuCoreNotFound extends RuntimeException
{
public static function create(): self
{
return new self(
'Could not find the number of CPU cores available.'
);
}
}
