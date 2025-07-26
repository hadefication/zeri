<?php declare(strict_types=1);








namespace PHPUnit\Event\Test;

use const PHP_EOL;
use function sprintf;
use function trim;
use PHPUnit\Event\Code\Test;
use PHPUnit\Event\Event;
use PHPUnit\Event\Telemetry;

/**
@immutable
@no-named-arguments

*/
final readonly class PhpunitErrorTriggered implements Event
{
private Telemetry\Info $telemetryInfo;
private Test $test;




private string $message;




public function __construct(Telemetry\Info $telemetryInfo, Test $test, string $message)
{
$this->telemetryInfo = $telemetryInfo;
$this->test = $test;
$this->message = $message;
}

public function telemetryInfo(): Telemetry\Info
{
return $this->telemetryInfo;
}

public function test(): Test
{
return $this->test;
}




public function message(): string
{
return $this->message;
}

public function asString(): string
{
$message = trim($this->message);

if (!empty($message)) {
$message = PHP_EOL . $message;
}

return sprintf(
'Test Triggered PHPUnit Error (%s)%s',
$this->test->id(),
$message,
);
}
}
