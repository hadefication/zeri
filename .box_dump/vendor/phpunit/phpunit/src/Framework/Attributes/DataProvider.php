<?php declare(strict_types=1);








namespace PHPUnit\Framework\Attributes;

use Attribute;

/**
@immutable
@no-named-arguments

*/
#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
final readonly class DataProvider
{



private string $methodName;




public function __construct(string $methodName)
{
$this->methodName = $methodName;
}




public function methodName(): string
{
return $this->methodName;
}
}
