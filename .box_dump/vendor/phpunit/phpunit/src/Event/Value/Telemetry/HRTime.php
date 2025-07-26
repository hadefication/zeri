<?php declare(strict_types=1);








namespace PHPUnit\Event\Telemetry;

use function sprintf;
use PHPUnit\Event\InvalidArgumentException;

/**
@immutable
@no-named-arguments

*/
final readonly class HRTime
{
private int $seconds;
private int $nanoseconds;




public static function fromSecondsAndNanoseconds(int $seconds, int $nanoseconds): self
{
return new self(
$seconds,
$nanoseconds,
);
}




private function __construct(int $seconds, int $nanoseconds)
{
$this->ensureNotNegative($seconds, 'seconds');
$this->ensureNotNegative($nanoseconds, 'nanoseconds');
$this->ensureNanoSecondsInRange($nanoseconds);

$this->seconds = $seconds;
$this->nanoseconds = $nanoseconds;
}

public function seconds(): int
{
return $this->seconds;
}

public function nanoseconds(): int
{
return $this->nanoseconds;
}

public function duration(self $start): Duration
{
$seconds = $this->seconds - $start->seconds();
$nanoseconds = $this->nanoseconds - $start->nanoseconds();

if ($nanoseconds < 0) {
$seconds--;

$nanoseconds += 1000000000;
}

if ($seconds < 0) {
return Duration::fromSecondsAndNanoseconds(0, 0);
}

return Duration::fromSecondsAndNanoseconds(
$seconds,
$nanoseconds,
);
}




private function ensureNotNegative(int $value, string $type): void
{
if ($value < 0) {
throw new InvalidArgumentException(
sprintf(
'Value for %s must not be negative.',
$type,
),
);
}
}




private function ensureNanoSecondsInRange(int $nanoseconds): void
{
if ($nanoseconds > 999999999) {
throw new InvalidArgumentException(
'Value for nanoseconds must not be greater than 999999999.',
);
}
}
}
