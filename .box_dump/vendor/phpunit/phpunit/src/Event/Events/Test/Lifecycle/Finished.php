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
final readonly class Finished implements Event
{
private Telemetry\Info $telemetryInfo;
private Code\Test $test;
private int $numberOfAssertionsPerformed;

public function __construct(Telemetry\Info $telemetryInfo, Code\Test $test, int $numberOfAssertionsPerformed)
{
$this->telemetryInfo = $telemetryInfo;
$this->test = $test;
$this->numberOfAssertionsPerformed = $numberOfAssertionsPerformed;
}

public function telemetryInfo(): Telemetry\Info
{
return $this->telemetryInfo;
}

public function test(): Code\Test
{
return $this->test;
}

public function numberOfAssertionsPerformed(): int
{
return $this->numberOfAssertionsPerformed;
}

public function asString(): string
{
return sprintf(
'Test Finished (%s)',
$this->test->id(),
);
}
}
