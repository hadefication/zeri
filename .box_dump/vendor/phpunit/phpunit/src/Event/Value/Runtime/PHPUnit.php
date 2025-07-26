<?php declare(strict_types=1);








namespace PHPUnit\Event\Runtime;

use PHPUnit\Runner\Version;

/**
@immutable
@no-named-arguments

*/
final readonly class PHPUnit
{
private string $versionId;
private string $releaseSeries;

public function __construct()
{
$this->versionId = Version::id();
$this->releaseSeries = Version::series();
}

public function versionId(): string
{
return $this->versionId;
}

public function releaseSeries(): string
{
return $this->releaseSeries;
}
}
