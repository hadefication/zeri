<?php

declare(strict_types=1);










namespace phpDocumentor\Reflection\PseudoTypes;

use phpDocumentor\Reflection\PseudoType;
use phpDocumentor\Reflection\Type;
use phpDocumentor\Reflection\Types\String_;

/**
@psalm-immutable


*/
final class TraitString extends String_ implements PseudoType
{
public function underlyingType(): Type
{
return new String_();
}




public function __toString(): string
{
return 'trait-string';
}
}
