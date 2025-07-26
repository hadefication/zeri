<?php declare(strict_types=1);








namespace PHPUnit\Framework\Constraint;

/**
@no-named-arguments
*/
abstract class Operator extends Constraint
{



abstract public function operator(): string;






abstract public function precedence(): int;




abstract public function arity(): int;




protected function checkConstraint(mixed $constraint): Constraint
{
if (!$constraint instanceof Constraint) {
return new IsEqual($constraint);
}

return $constraint;
}




protected function constraintNeedsParentheses(Constraint $constraint): bool
{
return $constraint instanceof self &&
$constraint->arity() > 1 &&
$this->precedence() <= $constraint->precedence();
}
}
