<?php declare(strict_types=1);








namespace PHPUnit\Event\Telemetry;

/**
@immutable
@no-named-arguments

*/
final readonly class MemoryUsage
{
private int $bytes;

public static function fromBytes(int $bytes): self
{
return new self($bytes);
}

private function __construct(int $bytes)
{
$this->bytes = $bytes;
}

public function bytes(): int
{
return $this->bytes;
}

public function diff(self $other): self
{
return self::fromBytes($this->bytes - $other->bytes);
}
}
