<?php declare(strict_types=1);








namespace PHPUnit\Framework\Constraint;

/**
@no-named-arguments
*/
final class IsNull extends Constraint
{



public function toString(): string
{
return 'is null';
}





protected function matches(mixed $other): bool
{
return $other === null;
}
}
