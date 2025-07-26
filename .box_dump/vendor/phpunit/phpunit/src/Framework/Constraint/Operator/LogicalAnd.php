<?php declare(strict_types=1);








namespace PHPUnit\Framework\Constraint;

/**
@no-named-arguments
*/
final class LogicalAnd extends BinaryOperator
{
public static function fromConstraints(mixed ...$constraints): self
{
return new self(...$constraints);
}




public function operator(): string
{
return 'and';
}






public function precedence(): int
{
return 22;
}





protected function matches(mixed $other): bool
{
foreach ($this->constraints() as $constraint) {
if (!$constraint->evaluate($other, '', true)) {
return false;
}
}

return [] !== $this->constraints();
}
}
