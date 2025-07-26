<?php declare(strict_types=1);








namespace PHPUnit\Event\Test;

use function sprintf;
use PHPUnit\Event\Code;
use PHPUnit\Event\Event;
use PHPUnit\Event\Telemetry;

/**
@immutable
@no-named-arguments

*/
final readonly class Prepared implements Event
{
private Telemetry\Info $telemetryInfo;
private Code\Test $test;

public function __construct(Telemetry\Info $telemetryInfo, Code\Test $test)
{
$this->telemetryInfo = $telemetryInfo;
$this->test = $test;
}

public function telemetryInfo(): Telemetry\Info
{
return $this->telemetryInfo;
}

public function test(): Code\Test
{
return $this->test;
}

public function asString(): string
{
return sprintf(
'Test Prepared (%s)',
$this->test->id(),
);
}
}
