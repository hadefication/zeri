<?php











declare(strict_types=1);

namespace Ramsey\Collection\Tool;

use function is_array;
use function is_bool;
use function is_callable;
use function is_float;
use function is_int;
use function is_numeric;
use function is_object;
use function is_resource;
use function is_scalar;
use function is_string;




trait TypeTrait
{






protected function checkType(string $type, mixed $value): bool
{
return match ($type) {
'array' => is_array($value),
'bool', 'boolean' => is_bool($value),
'callable' => is_callable($value),
'float', 'double' => is_float($value),
'int', 'integer' => is_int($value),
'null' => $value === null,
'numeric' => is_numeric($value),
'object' => is_object($value),
'resource' => is_resource($value),
'scalar' => is_scalar($value),
'string' => is_string($value),
'mixed' => true,
default => $value instanceof $type,
};
}
}
