<?php declare(strict_types=1);








namespace PHPUnit\Framework\Constraint;

use function sprintf;
use function strtr;

/**
@no-named-arguments
*/
final class StringEqualsStringIgnoringLineEndings extends Constraint
{
private readonly string $string;

public function __construct(string $string)
{
$this->string = $this->normalizeLineEndings($string);
}




public function toString(): string
{
return sprintf(
'is equal to "%s" ignoring line endings',
$this->string,
);
}





protected function matches(mixed $other): bool
{
return $this->string === $this->normalizeLineEndings((string) $other);
}

private function normalizeLineEndings(string $string): string
{
return strtr(
$string,
[
"\r\n" => "\n",
"\r" => "\n",
],
);
}
}
