<?php declare(strict_types=1);








namespace PHPUnit\Framework\Attributes;

use Attribute;

/**
@immutable
@no-named-arguments

*/
#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
final readonly class DependsOnClassUsingDeepClone
{



private string $className;




public function __construct(string $className)
{
$this->className = $className;
}




public function className(): string
{
return $this->className;
}
}
