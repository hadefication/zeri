<?php declare(strict_types=1);








namespace PHPUnit\Framework\MockObject;

use function sprintf;
use function strtolower;
use PHPUnit\Framework\Constraint\Constraint;

/**
@no-named-arguments


*/
final class MethodNameConstraint extends Constraint
{
private string $methodName;

public function __construct(string $methodName)
{
$this->methodName = $methodName;
}

public function toString(): string
{
return sprintf(
'is "%s"',
$this->methodName,
);
}

protected function matches(mixed $other): bool
{
return strtolower($this->methodName) === strtolower((string) $other);
}
}
