<?php declare(strict_types=1);








namespace PHPUnit\Event\Test;

use function sprintf;
use PHPUnit\Event\Event;
use PHPUnit\Event\Telemetry;

/**
@immutable
@no-named-arguments

*/
final readonly class MockObjectForTraitCreated implements Event
{
private Telemetry\Info $telemetryInfo;




private string $traitName;




public function __construct(Telemetry\Info $telemetryInfo, string $traitName)
{
$this->telemetryInfo = $telemetryInfo;
$this->traitName = $traitName;
}

public function telemetryInfo(): Telemetry\Info
{
return $this->telemetryInfo;
}




public function traitName(): string
{
return $this->traitName;
}

public function asString(): string
{
return sprintf(
'Mock Object Created (%s)',
$this->traitName,
);
}
}
