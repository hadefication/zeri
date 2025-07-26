<?php declare(strict_types=1);








namespace PHPUnit\Metadata;

use PHPUnit\Metadata\Version\Requirement;

/**
@immutable
@no-named-arguments

*/
final readonly class RequiresPhpunit extends Metadata
{
private Requirement $versionRequirement;




protected function __construct(int $level, Requirement $versionRequirement)
{
parent::__construct($level);

$this->versionRequirement = $versionRequirement;
}

public function isRequiresPhpunit(): true
{
return true;
}

public function versionRequirement(): Requirement
{
return $this->versionRequirement;
}
}
