<?php declare(strict_types=1);








namespace PHPUnit\Event\Runtime;

use const PHP_OS;
use const PHP_OS_FAMILY;

/**
@immutable
@no-named-arguments

*/
final readonly class OperatingSystem
{
private string $operatingSystem;
private string $operatingSystemFamily;

public function __construct()
{
$this->operatingSystem = PHP_OS;
$this->operatingSystemFamily = PHP_OS_FAMILY;
}

public function operatingSystem(): string
{
return $this->operatingSystem;
}

public function operatingSystemFamily(): string
{
return $this->operatingSystemFamily;
}
}
