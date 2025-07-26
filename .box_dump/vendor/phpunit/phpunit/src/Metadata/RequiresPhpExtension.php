<?php declare(strict_types=1);








namespace PHPUnit\Metadata;

use PHPUnit\Metadata\Version\Requirement;

/**
@immutable
@no-named-arguments

*/
final readonly class RequiresPhpExtension extends Metadata
{



private string $extension;
private ?Requirement $versionRequirement;





protected function __construct(int $level, string $extension, ?Requirement $versionRequirement)
{
parent::__construct($level);

$this->extension = $extension;
$this->versionRequirement = $versionRequirement;
}

public function isRequiresPhpExtension(): true
{
return true;
}




public function extension(): string
{
return $this->extension;
}

/**
@phpstan-assert-if-true
*/
public function hasVersionRequirement(): bool
{
return $this->versionRequirement !== null;
}




public function versionRequirement(): Requirement
{
if ($this->versionRequirement === null) {
throw new NoVersionRequirementException;
}

return $this->versionRequirement;
}
}
