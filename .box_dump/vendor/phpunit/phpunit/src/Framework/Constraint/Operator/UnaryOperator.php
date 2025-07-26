<?php declare(strict_types=1);








namespace PHPUnit\Framework\Constraint;

use function count;

/**
@no-named-arguments
*/
abstract class UnaryOperator extends Operator
{
private readonly Constraint $constraint;

public function __construct(mixed $constraint)
{
$this->constraint = $this->checkConstraint($constraint);
}




public function arity(): int
{
return 1;
}




public function toString(): string
{
$reduced = $this->reduce();

if ($reduced !== $this) {
return $reduced->toString();
}

$constraint = $this->constraint->reduce();

if ($this->constraintNeedsParentheses($constraint)) {
return $this->operator() . '( ' . $constraint->toString() . ' )';
}

$string = $constraint->toStringInContext($this, 0);

if ($string === '') {
return $this->transformString($constraint->toString());
}

return $string;
}




public function count(): int
{
return count($this->constraint);
}







protected function failureDescription(mixed $other): string
{
$reduced = $this->reduce();

if ($reduced !== $this) {
return $reduced->failureDescription($other);
}

$constraint = $this->constraint->reduce();

if ($this->constraintNeedsParentheses($constraint)) {
return $this->operator() . '( ' . $constraint->failureDescription($other) . ' )';
}

$string = $constraint->failureDescriptionInContext($this, 0, $other);

if ($string === '') {
return $this->transformString($constraint->failureDescription($other));
}

return $string;
}










protected function transformString(string $string): string
{
return $string;
}




final protected function constraint(): Constraint
{
return $this->constraint;
}




protected function constraintNeedsParentheses(Constraint $constraint): bool
{
$constraint = $constraint->reduce();

return $constraint instanceof self || parent::constraintNeedsParentheses($constraint);
}
}
