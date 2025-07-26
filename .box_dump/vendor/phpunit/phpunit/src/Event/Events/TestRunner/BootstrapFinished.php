<?php declare(strict_types=1);








namespace PHPUnit\Event\TestRunner;

use function sprintf;
use PHPUnit\Event\Event;
use PHPUnit\Event\Telemetry;

/**
@immutable
@no-named-arguments

*/
final readonly class BootstrapFinished implements Event
{
private Telemetry\Info $telemetryInfo;
private string $filename;

public function __construct(Telemetry\Info $telemetryInfo, string $filename)
{
$this->telemetryInfo = $telemetryInfo;
$this->filename = $filename;
}

public function telemetryInfo(): Telemetry\Info
{
return $this->telemetryInfo;
}

public function filename(): string
{
return $this->filename;
}

public function asString(): string
{
return sprintf(
'Bootstrap Finished (%s)',
$this->filename,
);
}
}
