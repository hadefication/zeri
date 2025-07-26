<?php

declare(strict_types=1);










namespace phpDocumentor\Reflection\Types;

use phpDocumentor\Reflection\Type;

/**
@psalm-immutable


*/
class String_ implements Type
{



public function __toString(): string
{
return 'string';
}
}
