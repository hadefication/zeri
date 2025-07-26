<?php declare(strict_types=1);








namespace PHPUnit\Event\Telemetry;

/**
@no-named-arguments


*/
interface MemoryMeter
{
public function memoryUsage(): MemoryUsage;

public function peakMemoryUsage(): MemoryUsage;
}
