<?php declare(strict_types=1);








namespace PHPUnit\Framework\Attributes;

use Attribute;

/**
@immutable
@no-named-arguments

*/
#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
final readonly class ExcludeGlobalVariableFromBackup
{



private string $globalVariableName;




public function __construct(string $globalVariableName)
{
$this->globalVariableName = $globalVariableName;
}




public function globalVariableName(): string
{
return $this->globalVariableName;
}
}
