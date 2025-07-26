<?php declare(strict_types=1);








namespace PHPUnit\Event\Runtime;

use const PHP_EXTRA_VERSION;
use const PHP_MAJOR_VERSION;
use const PHP_MINOR_VERSION;
use const PHP_RELEASE_VERSION;
use const PHP_SAPI;
use const PHP_VERSION;
use const PHP_VERSION_ID;
use function array_merge;
use function get_loaded_extensions;
use function sort;

/**
@immutable
@no-named-arguments

*/
final readonly class PHP
{
private string $version;
private int $versionId;
private int $majorVersion;
private int $minorVersion;
private int $releaseVersion;
private string $extraVersion;
private string $sapi;




private array $extensions;

public function __construct()
{
$this->version = PHP_VERSION;
$this->versionId = PHP_VERSION_ID;
$this->majorVersion = PHP_MAJOR_VERSION;
$this->minorVersion = PHP_MINOR_VERSION;
$this->releaseVersion = PHP_RELEASE_VERSION;
$this->extraVersion = PHP_EXTRA_VERSION;
$this->sapi = PHP_SAPI;

$extensions = array_merge(
get_loaded_extensions(true),
get_loaded_extensions(),
);

sort($extensions);

$this->extensions = $extensions;
}

public function version(): string
{
return $this->version;
}

public function sapi(): string
{
return $this->sapi;
}

public function majorVersion(): int
{
return $this->majorVersion;
}

public function minorVersion(): int
{
return $this->minorVersion;
}

public function releaseVersion(): int
{
return $this->releaseVersion;
}

public function extraVersion(): string
{
return $this->extraVersion;
}

public function versionId(): int
{
return $this->versionId;
}




public function extensions(): array
{
return $this->extensions;
}
}
