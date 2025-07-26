<?php declare(strict_types=1);








namespace PHPUnit\Framework\Constraint;

use function file_exists;
use function sprintf;

/**
@no-named-arguments
*/
final class FileExists extends Constraint
{



public function toString(): string
{
return 'file exists';
}





protected function matches(mixed $other): bool
{
return file_exists($other);
}







protected function failureDescription(mixed $other): string
{
return sprintf(
'file "%s" exists',
$other,
);
}
}
