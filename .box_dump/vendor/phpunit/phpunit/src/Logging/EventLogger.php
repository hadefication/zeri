<?php declare(strict_types=1);








namespace PHPUnit\Logging;

use const FILE_APPEND;
use const LOCK_EX;
use const PHP_EOL;
use const PHP_OS_FAMILY;
use function file_put_contents;
use function implode;
use function preg_split;
use function str_repeat;
use function strlen;
use PHPUnit\Event\Event;
use PHPUnit\Event\Tracer\Tracer;

/**
@no-named-arguments


*/
final readonly class EventLogger implements Tracer
{
private string $path;
private bool $includeTelemetryInfo;

public function __construct(string $path, bool $includeTelemetryInfo)
{
$this->path = $path;
$this->includeTelemetryInfo = $includeTelemetryInfo;
}

public function trace(Event $event): void
{
$telemetryInfo = $this->telemetryInfo($event);
$indentation = PHP_EOL . str_repeat(' ', strlen($telemetryInfo));
$lines = preg_split('/\r\n|\r|\n/', $event->asString());

$flags = FILE_APPEND;

if (!(PHP_OS_FAMILY === 'Windows' || PHP_OS_FAMILY === 'Darwin') ||
$this->path !== 'php://stdout') {
$flags |= LOCK_EX;
}

file_put_contents(
$this->path,
$telemetryInfo . implode($indentation, $lines) . PHP_EOL,
$flags,
);
}

private function telemetryInfo(Event $event): string
{
if (!$this->includeTelemetryInfo) {
return '';
}

return $event->telemetryInfo()->asString() . ' ';
}
}
