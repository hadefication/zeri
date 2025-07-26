<?php declare(strict_types=1);








namespace PHPUnit\Event\Telemetry;

/**
@no-named-arguments


*/
final readonly class System
{
private StopWatch $stopWatch;
private MemoryMeter $memoryMeter;
private GarbageCollectorStatusProvider $garbageCollectorStatusProvider;

public function __construct(StopWatch $stopWatch, MemoryMeter $memoryMeter, GarbageCollectorStatusProvider $garbageCollectorStatusProvider)
{
$this->stopWatch = $stopWatch;
$this->memoryMeter = $memoryMeter;
$this->garbageCollectorStatusProvider = $garbageCollectorStatusProvider;
}

public function snapshot(): Snapshot
{
return new Snapshot(
$this->stopWatch->current(),
$this->memoryMeter->memoryUsage(),
$this->memoryMeter->peakMemoryUsage(),
$this->garbageCollectorStatusProvider->status(),
);
}
}
