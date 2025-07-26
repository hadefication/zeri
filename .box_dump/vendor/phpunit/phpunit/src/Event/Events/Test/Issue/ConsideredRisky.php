<?php declare(strict_types=1);








namespace PHPUnit\Event\Test;

use const PHP_EOL;
use function sprintf;
use PHPUnit\Event\Code;
use PHPUnit\Event\Event;
use PHPUnit\Event\Telemetry;

/**
@immutable
@no-named-arguments

*/
final readonly class ConsideredRisky implements Event
{
private Telemetry\Info $telemetryInfo;
private Code\Test $test;




private string $message;




public function __construct(Telemetry\Info $telemetryInfo, Code\Test $test, string $message)
{
$this->telemetryInfo = $telemetryInfo;
$this->test = $test;
$this->message = $message;
}

public function telemetryInfo(): Telemetry\Info
{
return $this->telemetryInfo;
}

public function test(): Code\Test
{
return $this->test;
}




public function message(): string
{
return $this->message;
}

public function asString(): string
{
return sprintf(
'Test Considered Risky (%s)%s%s',
$this->test->id(),
PHP_EOL,
$this->message,
);
}
}
