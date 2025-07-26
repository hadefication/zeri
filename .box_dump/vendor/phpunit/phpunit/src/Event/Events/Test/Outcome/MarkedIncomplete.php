<?php declare(strict_types=1);








namespace PHPUnit\Event\Test;

use const PHP_EOL;
use function sprintf;
use function trim;
use PHPUnit\Event\Code;
use PHPUnit\Event\Code\Throwable;
use PHPUnit\Event\Event;
use PHPUnit\Event\Telemetry;

/**
@immutable
@no-named-arguments

*/
final readonly class MarkedIncomplete implements Event
{
private Telemetry\Info $telemetryInfo;
private Code\Test $test;
private Throwable $throwable;

public function __construct(Telemetry\Info $telemetryInfo, Code\Test $test, Throwable $throwable)
{
$this->telemetryInfo = $telemetryInfo;
$this->test = $test;
$this->throwable = $throwable;
}

public function telemetryInfo(): Telemetry\Info
{
return $this->telemetryInfo;
}

public function test(): Code\Test
{
return $this->test;
}

public function throwable(): Throwable
{
return $this->throwable;
}

public function asString(): string
{
$message = trim($this->throwable->message());

if (!empty($message)) {
$message = PHP_EOL . $message;
}

return sprintf(
'Test Marked Incomplete (%s)%s',
$this->test->id(),
$message,
);
}
}
