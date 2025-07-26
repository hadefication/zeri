<?php declare(strict_types=1);








namespace PHPUnit\Event\Telemetry;

use function floor;
use function sprintf;
use PHPUnit\Event\InvalidArgumentException;

/**
@immutable
@no-named-arguments

*/
final readonly class Duration
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

public function asFloat(): float
{
return $this->seconds() + ($this->nanoseconds() / 1000000000);
}

public function asString(): string
{
$seconds = $this->seconds();
$minutes = 0;
$hours = 0;

if ($seconds > 60 * 60) {
$hours = floor($seconds / 60 / 60);
$seconds -= ($hours * 60 * 60);
}

if ($seconds > 60) {
$minutes = floor($seconds / 60);
$seconds -= ($minutes * 60);
}

return sprintf(
'%02d:%02d:%02d.%09d',
$hours,
$minutes,
$seconds,
$this->nanoseconds(),
);
}

public function equals(self $other): bool
{
return $this->seconds === $other->seconds &&
$this->nanoseconds === $other->nanoseconds;
}

public function isLessThan(self $other): bool
{
if ($this->seconds < $other->seconds) {
return true;
}

if ($this->seconds > $other->seconds) {
return false;
}

return $this->nanoseconds < $other->nanoseconds;
}

public function isGreaterThan(self $other): bool
{
if ($this->seconds > $other->seconds) {
return true;
}

if ($this->seconds < $other->seconds) {
return false;
}

return $this->nanoseconds > $other->nanoseconds;
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
