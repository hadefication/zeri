<?php declare(strict_types=1);








namespace PHPUnit\Event\TestRunner;

use function sprintf;
use PHPUnit\Event\Event;
use PHPUnit\Event\Telemetry;

/**
@immutable
@no-named-arguments

*/
final readonly class ExtensionBootstrapped implements Event
{
private Telemetry\Info $telemetryInfo;




private string $className;




private array $parameters;





public function __construct(Telemetry\Info $telemetryInfo, string $className, array $parameters)
{
$this->telemetryInfo = $telemetryInfo;
$this->className = $className;
$this->parameters = $parameters;
}

public function telemetryInfo(): Telemetry\Info
{
return $this->telemetryInfo;
}




public function className(): string
{
return $this->className;
}




public function parameters(): array
{
return $this->parameters;
}

public function asString(): string
{
return sprintf(
'Extension Bootstrapped (%s)',
$this->className,
);
}
}
