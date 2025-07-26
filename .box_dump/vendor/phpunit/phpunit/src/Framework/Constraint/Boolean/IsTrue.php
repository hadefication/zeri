<?php declare(strict_types=1);








namespace PHPUnit\Framework\Constraint;

/**
@no-named-arguments
*/
final class IsTrue extends Constraint
{



public function toString(): string
{
return 'is true';
}





protected function matches(mixed $other): bool
{
return $other === true;
}
}
