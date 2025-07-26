<?php declare(strict_types=1);








namespace PHPUnit\Framework\Attributes;

use Attribute;

/**
@immutable
@no-named-arguments

*/
#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD)]
final readonly class RequiresPhp
{



private string $versionRequirement;




public function __construct(string $versionRequirement)
{
$this->versionRequirement = $versionRequirement;
}




public function versionRequirement(): string
{
return $this->versionRequirement;
}
}
