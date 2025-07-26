<?php declare(strict_types=1);








namespace PHPUnit\Framework\Constraint;

/**
@no-named-arguments
*/
final class IsFalse extends Constraint
{



public function toString(): string
{
return 'is false';
}





protected function matches(mixed $other): bool
{
return $other === false;
}
}
