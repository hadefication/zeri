<?php declare(strict_types=1);








namespace PHPUnit\Framework\Attributes;

use Attribute;

/**
@immutable
@no-named-arguments

*/
#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
final readonly class UsesClass
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
