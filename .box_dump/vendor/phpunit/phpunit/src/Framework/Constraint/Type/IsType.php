<?php declare(strict_types=1);








namespace PHPUnit\Framework\Constraint;

use function gettype;
use function is_array;
use function is_bool;
use function is_callable;
use function is_float;
use function is_int;
use function is_iterable;
use function is_numeric;
use function is_object;
use function is_scalar;
use function is_string;
use function sprintf;
use PHPUnit\Framework\UnknownTypeException;

/**
@no-named-arguments
*/
final class IsType extends Constraint
{



public const TYPE_ARRAY = 'array';




public const TYPE_BOOL = 'bool';




public const TYPE_FLOAT = 'float';




public const TYPE_INT = 'int';




public const TYPE_NULL = 'null';




public const TYPE_NUMERIC = 'numeric';




public const TYPE_OBJECT = 'object';




public const TYPE_RESOURCE = 'resource';




public const TYPE_CLOSED_RESOURCE = 'resource (closed)';




public const TYPE_STRING = 'string';




public const TYPE_SCALAR = 'scalar';




public const TYPE_CALLABLE = 'callable';




public const TYPE_ITERABLE = 'iterable';




private const KNOWN_TYPES = [
'array' => true,
'boolean' => true,
'bool' => true,
'double' => true,
'float' => true,
'integer' => true,
'int' => true,
'null' => true,
'numeric' => true,
'object' => true,
'real' => true,
'resource' => true,
'resource (closed)' => true,
'string' => true,
'scalar' => true,
'callable' => true,
'iterable' => true,
];




private readonly string $type;






public function __construct(string $type)
{
if (!isset(self::KNOWN_TYPES[$type])) {
throw new UnknownTypeException($type);
}

$this->type = $type;
}




public function toString(): string
{
return sprintf(
'is of type %s',
$this->type,
);
}





protected function matches(mixed $other): bool
{
switch ($this->type) {
case 'numeric':
return is_numeric($other);

case 'integer':
case 'int':
return is_int($other);

case 'double':
case 'float':
case 'real':
return is_float($other);

case 'string':
return is_string($other);

case 'boolean':
case 'bool':
return is_bool($other);

case 'null':
return null === $other;

case 'array':
return is_array($other);

case 'object':
return is_object($other);

case 'resource':
$type = gettype($other);

return $type === 'resource' || $type === 'resource (closed)';

case 'resource (closed)':
return gettype($other) === 'resource (closed)';

case 'scalar':
return is_scalar($other);

case 'callable':
return is_callable($other);

case 'iterable':
return is_iterable($other);

default:
return false;
}
}
}
