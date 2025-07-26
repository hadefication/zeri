<?php declare(strict_types=1);








namespace PHPUnit\Framework\MockObject\Runtime;

use function sprintf;

/**
@no-named-arguments
*/
final readonly class PropertyGetHook extends PropertyHook
{





public function asString(): string
{
return sprintf(
'$%s::get',
$this->propertyName(),
);
}
}
