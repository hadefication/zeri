<?php










namespace Symfony\Component\Clock;






final class NativeClock implements ClockInterface
{
private \DateTimeZone $timezone;




public function __construct(\DateTimeZone|string|null $timezone = null)
{
$this->timezone = \is_string($timezone ??= date_default_timezone_get()) ? $this->withTimeZone($timezone)->timezone : $timezone;
}

public function now(): DatePoint
{
return DatePoint::createFromInterface(new \DateTimeImmutable('now', $this->timezone));
}

public function sleep(float|int $seconds): void
{
if (0 < $s = (int) $seconds) {
sleep($s);
}

if (0 < $us = $seconds - $s) {
usleep((int) ($us * 1E6));
}
}




public function withTimeZone(\DateTimeZone|string $timezone): static
{
if (\PHP_VERSION_ID >= 80300 && \is_string($timezone)) {
$timezone = new \DateTimeZone($timezone);
} elseif (\is_string($timezone)) {
try {
$timezone = new \DateTimeZone($timezone);
} catch (\Exception $e) {
throw new \DateInvalidTimeZoneException($e->getMessage(), $e->getCode(), $e);
}
}

$clone = clone $this;
$clone->timezone = $timezone;

return $clone;
}
}
