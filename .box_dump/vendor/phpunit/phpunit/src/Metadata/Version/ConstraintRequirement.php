<?php declare(strict_types=1);








namespace PHPUnit\Metadata\Version;

use function preg_replace;
use PharIo\Version\Version;
use PharIo\Version\VersionConstraint;

/**
@immutable
@no-named-arguments

*/
final readonly class ConstraintRequirement extends Requirement
{
private VersionConstraint $constraint;

public function __construct(VersionConstraint $constraint)
{
$this->constraint = $constraint;
}

public function isSatisfiedBy(string $version): bool
{
return $this->constraint->complies(
new Version($this->sanitize($version)),
);
}

public function asString(): string
{
return $this->constraint->asString();
}

private function sanitize(string $version): string
{
return preg_replace(
'/^(\d+\.\d+(?:.\d+)?).*$/',
'$1',
$version,
);
}
}
