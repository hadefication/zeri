<?php










namespace Symfony\Component\Clock;

use Psr\Clock\ClockInterface as PsrClockInterface;




interface ClockInterface extends PsrClockInterface
{
public function sleep(float|int $seconds): void;

public function withTimeZone(\DateTimeZone|string $timezone): static;
}
