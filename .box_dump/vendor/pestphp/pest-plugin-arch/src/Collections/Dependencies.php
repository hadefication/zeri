<?php

declare(strict_types=1);

namespace Pest\Arch\Collections;

use Pest\Arch\ValueObjects\Dependency;
use Stringable;




final class Dependencies implements Stringable
{





public function __construct(
public readonly array $values,
) {

}






public static function fromExpectationInput(array|string $values): self
{
return new self(array_map(
static fn (string $value): Dependency => Dependency::fromString($value),
is_array($values) ? $values : [$values]
));
}




public function __toString(): string
{
return implode(', ', array_map(
static fn (Dependency $dependency): string => $dependency->value,
$this->values
));
}
}
