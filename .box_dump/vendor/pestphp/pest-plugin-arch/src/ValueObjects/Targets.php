<?php

declare(strict_types=1);

namespace Pest\Arch\ValueObjects;

use Pest\Expectation;
use PHPUnit\Framework\ExpectationFailedException;




final class Targets
{





public function __construct(
public readonly array $value,
) {

}






public static function fromExpectation(Expectation $expectation): self
{
assert(is_string($expectation->value) || is_array($expectation->value)); 

$values = is_string($expectation->value) ? [$expectation->value] : $expectation->value;

foreach ($values as $value) {
if (str_contains($value, '/')) {
throw new ExpectationFailedException(
"Expecting '{$value}' to be a class name or namespace, but it contains a path.",
);
}
}

return new self($values);
}
}
