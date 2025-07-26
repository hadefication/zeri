<?php declare(strict_types=1);








namespace PHPUnit\Event\TestRunner;

use PHPUnit\Event\Event;
use PHPUnit\Event\Telemetry;

/**
@immutable
@no-named-arguments

*/
final readonly class ChildProcessFinished implements Event
{
private Telemetry\Info $telemetryInfo;
private string $stdout;
private string $stderr;

public function __construct(Telemetry\Info $telemetryInfo, string $stdout, string $stderr)
{
$this->telemetryInfo = $telemetryInfo;
$this->stdout = $stdout;
$this->stderr = $stderr;
}

public function telemetryInfo(): Telemetry\Info
{
return $this->telemetryInfo;
}

public function stdout(): string
{
return $this->stdout;
}

public function stderr(): string
{
return $this->stderr;
}

public function asString(): string
{
return 'Child Process Finished';
}
}
