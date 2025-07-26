<?php declare(strict_types=1);








namespace PHPUnit\Event\TestSuite;

use PHPUnit\Event\Event;
use PHPUnit\Event\Telemetry;

/**
@immutable
@no-named-arguments

*/
final readonly class Sorted implements Event
{
private Telemetry\Info $telemetryInfo;
private int $executionOrder;
private int $executionOrderDefects;
private bool $resolveDependencies;

public function __construct(Telemetry\Info $telemetryInfo, int $executionOrder, int $executionOrderDefects, bool $resolveDependencies)
{
$this->telemetryInfo = $telemetryInfo;
$this->executionOrder = $executionOrder;
$this->executionOrderDefects = $executionOrderDefects;
$this->resolveDependencies = $resolveDependencies;
}

public function telemetryInfo(): Telemetry\Info
{
return $this->telemetryInfo;
}

public function executionOrder(): int
{
return $this->executionOrder;
}

public function executionOrderDefects(): int
{
return $this->executionOrderDefects;
}

public function resolveDependencies(): bool
{
return $this->resolveDependencies;
}

public function asString(): string
{
return 'Test Suite Sorted';
}
}
