<?php declare(strict_types=1);








namespace PHPUnit\Framework\Constraint;

use function array_map;
use function count;

/**
@no-named-arguments
*/
abstract class BinaryOperator extends Operator
{



private readonly array $constraints;

protected function __construct(mixed ...$constraints)
{
$this->constraints = array_map(
fn ($constraint): Constraint => $this->checkConstraint($constraint),
$constraints,
);
}




final public function arity(): int
{
return count($this->constraints);
}




public function toString(): string
{
$reduced = $this->reduce();

if ($reduced !== $this) {
return $reduced->toString();
}

$text = '';

foreach ($this->constraints as $key => $constraint) {
$constraint = $constraint->reduce();

$text .= $this->constraintToString($constraint, $key);
}

return $text;
}




public function count(): int
{
$count = 0;

foreach ($this->constraints as $constraint) {
$count += count($constraint);
}

return $count;
}




final protected function constraints(): array
{
return $this->constraints;
}




final protected function constraintNeedsParentheses(Constraint $constraint): bool
{
return $this->arity() > 1 && parent::constraintNeedsParentheses($constraint);
}








protected function reduce(): Constraint
{
if ($this->arity() === 1 && $this->constraints[0] instanceof Operator) {
return $this->constraints[0]->reduce();
}

return parent::reduce();
}




private function constraintToString(Constraint $constraint, int $position): string
{
$prefix = '';

if ($position > 0) {
$prefix = (' ' . $this->operator() . ' ');
}

if ($this->constraintNeedsParentheses($constraint)) {
return $prefix . '( ' . $constraint->toString() . ' )';
}

$string = $constraint->toStringInContext($this, $position);

if ($string === '') {
$string = $constraint->toString();
}

return $prefix . $string;
}
}
