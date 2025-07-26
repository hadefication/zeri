<?php declare(strict_types=1);








namespace PHPUnit\Framework\Attributes;

use Attribute;

/**
@immutable
@no-named-arguments

*/
#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
final readonly class Group
{



private string $name;




public function __construct(string $name)
{
$this->name = $name;
}




public function name(): string
{
return $this->name;
}
}
