<?php declare(strict_types=1);








namespace PHPUnit\Framework\Constraint;

use function array_is_list;
use function is_array;

/**
@no-named-arguments
*/
final class IsList extends Constraint
{



public function toString(): string
{
return 'is a list';
}





protected function matches(mixed $other): bool
{
if (!is_array($other)) {
return false;
}

return array_is_list($other);
}







protected function failureDescription(mixed $other): string
{
return $this->valueToTypeStringFragment($other) . $this->toString();
}
}
