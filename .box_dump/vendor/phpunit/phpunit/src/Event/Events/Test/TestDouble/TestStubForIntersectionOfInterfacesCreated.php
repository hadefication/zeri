<?php declare(strict_types=1);








namespace PHPUnit\Event\Test;

use function implode;
use function sprintf;
use PHPUnit\Event\Event;
use PHPUnit\Event\Telemetry;

/**
@immutable
@no-named-arguments

*/
final readonly class TestStubForIntersectionOfInterfacesCreated implements Event
{
private Telemetry\Info $telemetryInfo;




private array $interfaces;




public function __construct(Telemetry\Info $telemetryInfo, array $interfaces)
{
$this->telemetryInfo = $telemetryInfo;
$this->interfaces = $interfaces;
}

public function telemetryInfo(): Telemetry\Info
{
return $this->telemetryInfo;
}




public function interfaces(): array
{
return $this->interfaces;
}

public function asString(): string
{
return sprintf(
'Test Stub Created (%s)',
implode('&', $this->interfaces),
);
}
}
