<?php declare(strict_types=1);








namespace PHPUnit\Framework\MockObject;

use function get_debug_type;
use function sprintf;

/**
@no-named-arguments


*/
final class IncompatibleReturnValueException extends \PHPUnit\Framework\Exception implements Exception
{
public function __construct(ConfigurableMethod $method, mixed $value)
{
parent::__construct(
sprintf(
'Method %s may not return value of type %s, its declared return type is "%s"',
$method->name(),
get_debug_type($value),
$method->returnTypeDeclaration(),
),
);
}
}
