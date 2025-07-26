<?php declare(strict_types=1);








namespace PHPUnit\Event\Test;

use const PHP_EOL;
use function implode;
use function sprintf;
use PHPUnit\Event\Code\Test;
use PHPUnit\Event\Event;
use PHPUnit\Event\Telemetry;

/**
@immutable
@no-named-arguments

*/
final readonly class PhpNoticeTriggered implements Event
{
private Telemetry\Info $telemetryInfo;
private Test $test;




private string $message;




private string $file;




private int $line;
private bool $suppressed;
private bool $ignoredByBaseline;






public function __construct(Telemetry\Info $telemetryInfo, Test $test, string $message, string $file, int $line, bool $suppressed, bool $ignoredByBaseline)
{
$this->telemetryInfo = $telemetryInfo;
$this->test = $test;
$this->message = $message;
$this->file = $file;
$this->line = $line;
$this->suppressed = $suppressed;
$this->ignoredByBaseline = $ignoredByBaseline;
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




public function file(): string
{
return $this->file;
}




public function line(): int
{
return $this->line;
}

public function wasSuppressed(): bool
{
return $this->suppressed;
}

public function ignoredByBaseline(): bool
{
return $this->ignoredByBaseline;
}

public function asString(): string
{
$message = $this->message;

if (!empty($message)) {
$message = PHP_EOL . $message;
}

$details = [$this->test->id()];

if ($this->suppressed) {
$details[] = 'suppressed using operator';
}

if ($this->ignoredByBaseline) {
$details[] = 'ignored by baseline';
}

return sprintf(
'Test Triggered PHP Notice (%s) in %s:%d%s',
implode(', ', $details),
$this->file,
$this->line,
$message,
);
}
}
