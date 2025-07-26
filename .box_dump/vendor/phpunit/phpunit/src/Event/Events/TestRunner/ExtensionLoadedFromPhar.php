<?php declare(strict_types=1);








namespace PHPUnit\Event\TestRunner;

use function sprintf;
use PHPUnit\Event\Event;
use PHPUnit\Event\Telemetry;

/**
@immutable
@no-named-arguments

*/
final readonly class ExtensionLoadedFromPhar implements Event
{
private Telemetry\Info $telemetryInfo;
private string $filename;
private string $name;
private string $version;

public function __construct(Telemetry\Info $telemetryInfo, string $filename, string $name, string $version)
{
$this->telemetryInfo = $telemetryInfo;
$this->filename = $filename;
$this->name = $name;
$this->version = $version;
}

public function telemetryInfo(): Telemetry\Info
{
return $this->telemetryInfo;
}

public function filename(): string
{
return $this->filename;
}

public function name(): string
{
return $this->name;
}

public function version(): string
{
return $this->version;
}

public function asString(): string
{
return sprintf(
'Extension Loaded from PHAR (%s %s)',
$this->name,
$this->version,
);
}
}
