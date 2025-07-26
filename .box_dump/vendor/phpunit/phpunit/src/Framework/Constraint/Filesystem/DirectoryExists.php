<?php declare(strict_types=1);








namespace PHPUnit\Framework\Constraint;

use function is_dir;
use function sprintf;

/**
@no-named-arguments
*/
final class DirectoryExists extends Constraint
{



public function toString(): string
{
return 'directory exists';
}





protected function matches(mixed $other): bool
{
return is_dir($other);
}







protected function failureDescription(mixed $other): string
{
return sprintf(
'directory "%s" exists',
$other,
);
}
}
