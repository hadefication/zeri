<?php declare(strict_types=1);








namespace PHPUnit\Framework\Constraint;

use function class_exists;
use function interface_exists;
use function sprintf;
use PHPUnit\Framework\UnknownClassOrInterfaceException;

/**
@no-named-arguments
*/
final class IsInstanceOf extends Constraint
{



private readonly string $name;




private readonly string $type;




public function __construct(string $name)
{
if (class_exists($name)) {
$this->type = 'class';
} elseif (interface_exists($name)) {
$this->type = 'interface';
} else {
throw new UnknownClassOrInterfaceException($name);
}

$this->name = $name;
}




public function toString(): string
{
return sprintf(
'is an instance of %s %s',
$this->type,
$this->name,
);
}





protected function matches(mixed $other): bool
{
return $other instanceof $this->name;
}







protected function failureDescription(mixed $other): string
{
return $this->valueToTypeStringFragment($other) . $this->toString();
}
}
