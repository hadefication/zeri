<?php declare(strict_types=1);








namespace PHPUnit\Framework\Constraint;

use function is_infinite;

/**
@no-named-arguments
*/
final class IsInfinite extends Constraint
{



public function toString(): string
{
return 'is infinite';
}





protected function matches(mixed $other): bool
{
return is_infinite($other);
}
}
