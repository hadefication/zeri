<?php declare(strict_types=1);








namespace PHPUnit\Metadata\Version;

use function version_compare;
use PHPUnit\Util\VersionComparisonOperator;

/**
@immutable
@no-named-arguments

*/
final readonly class ComparisonRequirement extends Requirement
{
private string $version;
private VersionComparisonOperator $operator;

public function __construct(string $version, VersionComparisonOperator $operator)
{
$this->version = $version;
$this->operator = $operator;
}

public function isSatisfiedBy(string $version): bool
{
return version_compare($version, $this->version, $this->operator->asString());
}

public function asString(): string
{
return $this->operator->asString() . ' ' . $this->version;
}
}
