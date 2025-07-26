<?php declare(strict_types=1);








namespace PHPUnit\Framework\Constraint;

use function str_starts_with;
use PHPUnit\Framework\EmptyStringException;

/**
@no-named-arguments
*/
final class StringStartsWith extends Constraint
{
private readonly string $prefix;




public function __construct(string $prefix)
{
if ($prefix === '') {
throw new EmptyStringException;
}

$this->prefix = $prefix;
}




public function toString(): string
{
return 'starts with "' . $this->prefix . '"';
}





protected function matches(mixed $other): bool
{
return str_starts_with((string) $other, $this->prefix);
}
}
