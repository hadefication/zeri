<?php

declare(strict_types=1);










namespace phpDocumentor\Reflection\PseudoTypes;

use phpDocumentor\Reflection\PseudoType;
use phpDocumentor\Reflection\Type;
use phpDocumentor\Reflection\Types\Boolean;

use function class_alias;

/**
@psalm-immutable


*/
final class True_ extends Boolean implements PseudoType
{
public function underlyingType(): Type
{
return new Boolean();
}

public function __toString(): string
{
return 'true';
}
}

class_alias(True_::class, 'phpDocumentor\Reflection\Types\True_', false);
