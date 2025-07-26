<?php










declare(strict_types=1);

namespace Fidry\CpuCoreCounter\Finder;

use function getenv;
use function preg_match;
use function sprintf;
use function var_export;

final class EnvVariableFinder implements CpuCoreFinder
{

private $environmentVariableName;

public function __construct(string $environmentVariableName)
{
$this->environmentVariableName = $environmentVariableName;
}

public function diagnose(): string
{
$value = getenv($this->environmentVariableName);

return sprintf(
'parse(getenv(%s)=%s)=%s',
$this->environmentVariableName,
var_export($value, true),
self::isPositiveInteger($value) ? $value : 'null'
);
}

public function find(): ?int
{
$value = getenv($this->environmentVariableName);

return self::isPositiveInteger($value)
? (int) $value
: null;
}

public function toString(): string
{
return sprintf(
'getenv(%s)',
$this->environmentVariableName
);
}




private static function isPositiveInteger($value): bool
{
return false !== $value
&& 1 === preg_match('/^\d+$/', $value)
&& (int) $value > 0;
}
}
