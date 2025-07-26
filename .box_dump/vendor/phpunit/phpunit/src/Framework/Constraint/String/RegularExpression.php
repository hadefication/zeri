<?php declare(strict_types=1);








namespace PHPUnit\Framework\Constraint;

use function preg_match;
use function sprintf;

/**
@no-named-arguments
*/
final class RegularExpression extends Constraint
{
private readonly string $pattern;

public function __construct(string $pattern)
{
$this->pattern = $pattern;
}




public function toString(): string
{
return sprintf(
'matches PCRE pattern "%s"',
$this->pattern,
);
}





protected function matches(mixed $other): bool
{
return preg_match($this->pattern, $other) > 0;
}
}
