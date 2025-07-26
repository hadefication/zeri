<?php declare(strict_types=1);








namespace PHPUnit\TextUI\Configuration;

use PHPUnit\Util\VersionComparisonOperator;

/**
@no-named-arguments
@immutable

*/
final readonly class TestDirectory
{



private string $path;
private string $prefix;
private string $suffix;
private string $phpVersion;
private VersionComparisonOperator $phpVersionOperator;




private array $groups;





public function __construct(string $path, string $prefix, string $suffix, string $phpVersion, VersionComparisonOperator $phpVersionOperator, array $groups)
{
$this->path = $path;
$this->prefix = $prefix;
$this->suffix = $suffix;
$this->phpVersion = $phpVersion;
$this->phpVersionOperator = $phpVersionOperator;
$this->groups = $groups;
}




public function path(): string
{
return $this->path;
}

public function prefix(): string
{
return $this->prefix;
}

public function suffix(): string
{
return $this->suffix;
}

public function phpVersion(): string
{
return $this->phpVersion;
}

public function phpVersionOperator(): VersionComparisonOperator
{
return $this->phpVersionOperator;
}




public function groups(): array
{
return $this->groups;
}
}
