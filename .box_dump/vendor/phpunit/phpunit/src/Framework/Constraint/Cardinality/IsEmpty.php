<?php declare(strict_types=1);








namespace PHPUnit\Framework\Constraint;

use function count;
use function gettype;
use function sprintf;
use function str_starts_with;
use Countable;
use EmptyIterator;

/**
@no-named-arguments
*/
final class IsEmpty extends Constraint
{



public function toString(): string
{
return 'is empty';
}





protected function matches(mixed $other): bool
{
if ($other instanceof EmptyIterator) {
return true;
}

if ($other instanceof Countable) {
return count($other) === 0;
}

return empty($other);
}







protected function failureDescription(mixed $other): string
{
$type = gettype($other);

return sprintf(
'%s %s %s',
str_starts_with($type, 'a') || str_starts_with($type, 'o') ? 'an' : 'a',
$type,
$this->toString(),
);
}
}
