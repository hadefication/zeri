<?php declare(strict_types=1);








namespace PHPUnit\Framework\Constraint;

use function is_finite;

/**
@no-named-arguments
*/
final class IsFinite extends Constraint
{



public function toString(): string
{
return 'is finite';
}





protected function matches(mixed $other): bool
{
return is_finite($other);
}
}
