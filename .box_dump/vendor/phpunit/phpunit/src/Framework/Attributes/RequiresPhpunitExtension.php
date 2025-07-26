<?php declare(strict_types=1);








namespace PHPUnit\Framework\Attributes;

use Attribute;
use PHPUnit\Runner\Extension\Extension;

/**
@immutable
@no-named-arguments

*/
#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
final readonly class RequiresPhpunitExtension
{



private string $extensionClass;




public function __construct(string $extensionClass)
{
$this->extensionClass = $extensionClass;
}




public function extensionClass(): string
{
return $this->extensionClass;
}
}
