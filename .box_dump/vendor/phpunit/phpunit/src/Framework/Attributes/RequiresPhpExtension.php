<?php declare(strict_types=1);








namespace PHPUnit\Framework\Attributes;

use Attribute;

/**
@immutable
@no-named-arguments

*/
#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
final readonly class RequiresPhpExtension
{



private string $extension;




private ?string $versionRequirement;





public function __construct(string $extension, ?string $versionRequirement = null)
{
$this->extension = $extension;
$this->versionRequirement = $versionRequirement;
}




public function extension(): string
{
return $this->extension;
}




public function versionRequirement(): ?string
{
return $this->versionRequirement;
}
}
