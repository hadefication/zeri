<?php declare(strict_types=1);








namespace PHPUnit\Event\TestSuite;

use function sprintf;
use PHPUnit\Event\Event;
use PHPUnit\Event\Telemetry;

/**
@immutable
@no-named-arguments

*/
final readonly class Skipped implements Event
{
private Telemetry\Info $telemetryInfo;
private TestSuite $testSuite;
private string $message;

public function __construct(Telemetry\Info $telemetryInfo, TestSuite $testSuite, string $message)
{
$this->telemetryInfo = $telemetryInfo;
$this->testSuite = $testSuite;
$this->message = $message;
}

public function telemetryInfo(): Telemetry\Info
{
return $this->telemetryInfo;
}

public function testSuite(): TestSuite
{
return $this->testSuite;
}

public function message(): string
{
return $this->message;
}

public function asString(): string
{
return sprintf(
'Test Suite Skipped (%s, %s)',
$this->testSuite->name(),
$this->message,
);
}
}
