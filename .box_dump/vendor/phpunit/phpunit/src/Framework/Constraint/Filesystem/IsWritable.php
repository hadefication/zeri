<?php declare(strict_types=1);








namespace PHPUnit\Framework\Constraint;

use function is_writable;
use function sprintf;

/**
@no-named-arguments
*/
final class IsWritable extends Constraint
{



public function toString(): string
{
return 'is writable';
}





protected function matches(mixed $other): bool
{
return is_writable($other);
}







protected function failureDescription(mixed $other): string
{
return sprintf(
'"%s" is writable',
$other,
);
}
}
