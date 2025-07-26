<?php declare(strict_types=1);








namespace PHPUnit\Framework\Constraint;

use function gettype;
use function is_object;
use function sprintf;
use ReflectionObject;

/**
@no-named-arguments
*/
final class ObjectHasProperty extends Constraint
{
private readonly string $propertyName;

public function __construct(string $propertyName)
{
$this->propertyName = $propertyName;
}




public function toString(): string
{
return sprintf(
'has property "%s"',
$this->propertyName,
);
}







protected function matches(mixed $other): bool
{
if (!is_object($other)) {
return false;
}

return (new ReflectionObject($other))->hasProperty($this->propertyName);
}









protected function failureDescription(mixed $other): string
{
if (is_object($other)) {
return sprintf(
'object of class "%s" %s',
$other::class,
$this->toString(),
);
}

return sprintf(
'"%s" (%s) %s',
$other,
gettype($other),
$this->toString(),
);
}
}
