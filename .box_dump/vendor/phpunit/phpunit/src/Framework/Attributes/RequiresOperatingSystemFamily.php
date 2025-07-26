<?php declare(strict_types=1);








namespace PHPUnit\Framework\Attributes;

use Attribute;

/**
@immutable
@no-named-arguments

*/
#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD)]
final readonly class RequiresOperatingSystemFamily
{



private string $operatingSystemFamily;




public function __construct(string $operatingSystemFamily)
{
$this->operatingSystemFamily = $operatingSystemFamily;
}




public function operatingSystemFamily(): string
{
return $this->operatingSystemFamily;
}
}
