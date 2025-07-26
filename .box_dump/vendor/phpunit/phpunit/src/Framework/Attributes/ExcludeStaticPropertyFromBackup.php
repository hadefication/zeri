<?php declare(strict_types=1);








namespace PHPUnit\Framework\Attributes;

use Attribute;

/**
@immutable
@no-named-arguments

*/
#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
final readonly class ExcludeStaticPropertyFromBackup
{



private string $className;




private string $propertyName;





public function __construct(string $className, string $propertyName)
{
$this->className = $className;
$this->propertyName = $propertyName;
}




public function className(): string
{
return $this->className;
}




public function propertyName(): string
{
return $this->propertyName;
}
}
