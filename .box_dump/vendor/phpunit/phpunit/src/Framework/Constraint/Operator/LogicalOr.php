<?php declare(strict_types=1);








namespace PHPUnit\Framework\Constraint;

/**
@no-named-arguments
*/
final class LogicalOr extends BinaryOperator
{
public static function fromConstraints(mixed ...$constraints): self
{
return new self(...$constraints);
}




public function operator(): string
{
return 'or';
}






public function precedence(): int
{
return 24;
}





public function matches(mixed $other): bool
{
foreach ($this->constraints() as $constraint) {
if ($constraint->evaluate($other, '', true)) {
return true;
}
}

return false;
}
}
