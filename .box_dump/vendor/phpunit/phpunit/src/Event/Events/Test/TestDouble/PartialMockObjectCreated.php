<?php declare(strict_types=1);








namespace PHPUnit\Event\Test;

use function sprintf;
use PHPUnit\Event\Event;
use PHPUnit\Event\Telemetry;

/**
@immutable
@no-named-arguments

*/
final readonly class PartialMockObjectCreated implements Event
{
private Telemetry\Info $telemetryInfo;




private string $className;




private array $methodNames;




public function __construct(Telemetry\Info $telemetryInfo, string $className, string ...$methodNames)
{
$this->telemetryInfo = $telemetryInfo;
$this->className = $className;
$this->methodNames = $methodNames;
}

public function telemetryInfo(): Telemetry\Info
{
return $this->telemetryInfo;
}




public function className(): string
{
return $this->className;
}




public function methodNames(): array
{
return $this->methodNames;
}

public function asString(): string
{
return sprintf(
'Partial Mock Object Created (%s)',
$this->className,
);
}
}
