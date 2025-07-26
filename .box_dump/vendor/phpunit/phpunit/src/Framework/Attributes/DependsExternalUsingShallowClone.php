<?php declare(strict_types=1);








namespace PHPUnit\Framework\Attributes;

use Attribute;

/**
@immutable
@no-named-arguments

*/
#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
final readonly class DependsExternalUsingShallowClone
{



private string $className;




private string $methodName;





public function __construct(string $className, string $methodName)
{
$this->className = $className;
$this->methodName = $methodName;
}




public function className(): string
{
return $this->className;
}




public function methodName(): string
{
return $this->methodName;
}
}
