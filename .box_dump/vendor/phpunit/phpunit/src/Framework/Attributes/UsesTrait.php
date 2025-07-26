<?php declare(strict_types=1);








namespace PHPUnit\Framework\Attributes;

use Attribute;

/**
@immutable
@no-named-arguments

*/
#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
final readonly class UsesTrait
{



private string $traitName;




public function __construct(string $traitName)
{
$this->traitName = $traitName;
}




public function traitName(): string
{
return $this->traitName;
}
}
