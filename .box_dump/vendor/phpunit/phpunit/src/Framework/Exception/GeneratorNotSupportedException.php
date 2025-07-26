<?php declare(strict_types=1);








namespace PHPUnit\Framework;

use function sprintf;

/**
@no-named-arguments


*/
final class GeneratorNotSupportedException extends InvalidArgumentException
{
public static function fromParameterName(string $parameterName): self
{
return new self(
sprintf(
'Passing an argument of type Generator for the %s parameter is not supported',
$parameterName,
),
);
}
}
