<?php declare(strict_types=1);








namespace PHPUnit\Framework\Constraint;

use function is_readable;
use function sprintf;

/**
@no-named-arguments
*/
final class IsReadable extends Constraint
{



public function toString(): string
{
return 'is readable';
}





protected function matches(mixed $other): bool
{
return is_readable($other);
}







protected function failureDescription(mixed $other): string
{
return sprintf(
'"%s" is readable',
$other,
);
}
}
