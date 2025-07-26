<?php declare(strict_types=1);








namespace PHPUnit\Framework\Constraint;

use function array_reduce;
use function array_shift;
use PHPUnit\Framework\ExpectationFailedException;

/**
@no-named-arguments
*/
final class LogicalXor extends BinaryOperator
{
public static function fromConstraints(mixed ...$constraints): self
{
return new self(...$constraints);
}




public function operator(): string
{
return 'xor';
}






public function precedence(): int
{
return 23;
}







public function matches(mixed $other): bool
{
$constraints = $this->constraints();

$initial = array_shift($constraints);

if ($initial === null) {
return false;
}

return array_reduce(
$constraints,
static fn (bool $matches, Constraint $constraint): bool => $matches xor $constraint->evaluate($other, '', true),
$initial->evaluate($other, '', true),
);
}
}
