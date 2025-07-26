<?php declare(strict_types=1);








namespace PHPUnit\Event\Telemetry;

use function memory_get_peak_usage;
use function memory_get_usage;

/**
@no-named-arguments


*/
final readonly class SystemMemoryMeter implements MemoryMeter
{
public function memoryUsage(): MemoryUsage
{
return MemoryUsage::fromBytes(memory_get_usage(true));
}

public function peakMemoryUsage(): MemoryUsage
{
return MemoryUsage::fromBytes(memory_get_peak_usage(true));
}
}
