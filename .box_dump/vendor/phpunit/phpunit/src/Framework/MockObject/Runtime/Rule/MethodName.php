<?php declare(strict_types=1);








namespace PHPUnit\Framework\MockObject\Rule;

use function is_string;
use PHPUnit\Framework\Constraint\Constraint;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\InvalidArgumentException;
use PHPUnit\Framework\MockObject\Invocation as BaseInvocation;
use PHPUnit\Framework\MockObject\MethodNameConstraint;

/**
@no-named-arguments


*/
final readonly class MethodName
{
private Constraint $constraint;




public function __construct(Constraint|string $constraint)
{
if (is_string($constraint)) {
$constraint = new MethodNameConstraint($constraint);
}

$this->constraint = $constraint;
}

public function toString(): string
{
return 'method name ' . $this->constraint->toString();
}




public function matches(BaseInvocation $invocation): bool
{
return $this->matchesName($invocation->methodName());
}




public function matchesName(string $methodName): bool
{
return (bool) $this->constraint->evaluate($methodName, '', true);
}
}
