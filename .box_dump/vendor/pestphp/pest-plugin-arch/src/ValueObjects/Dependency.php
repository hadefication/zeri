<?php

declare(strict_types=1);

namespace Pest\Arch\ValueObjects;

use PHPUnit\Framework\ExpectationFailedException;




final class Dependency
{



public function __construct(
public readonly string $value,
) {

}




public static function fromString(string $value): self
{
if (str_contains($value, '/')) {
throw new ExpectationFailedException(
"Expecting '{$value}' to be a class name or namespace, but it contains a path.",
);
}

return new self($value);
}
}
