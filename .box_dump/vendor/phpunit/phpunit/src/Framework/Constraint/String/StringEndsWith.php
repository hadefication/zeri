<?php declare(strict_types=1);








namespace PHPUnit\Framework\Constraint;

use function str_ends_with;
use PHPUnit\Framework\EmptyStringException;

/**
@no-named-arguments
*/
final class StringEndsWith extends Constraint
{
private readonly string $suffix;




public function __construct(string $suffix)
{
if ($suffix === '') {
throw new EmptyStringException;
}

$this->suffix = $suffix;
}




public function toString(): string
{
return 'ends with "' . $this->suffix . '"';
}





protected function matches(mixed $other): bool
{
return str_ends_with((string) $other, $this->suffix);
}
}
