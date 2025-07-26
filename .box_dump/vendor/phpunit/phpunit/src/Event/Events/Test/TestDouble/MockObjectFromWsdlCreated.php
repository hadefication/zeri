<?php declare(strict_types=1);








namespace PHPUnit\Event\Test;

use function sprintf;
use PHPUnit\Event\Event;
use PHPUnit\Event\Telemetry;

/**
@immutable
@no-named-arguments

*/
final readonly class MockObjectFromWsdlCreated implements Event
{
private Telemetry\Info $telemetryInfo;
private string $wsdlFile;




private string $originalClassName;




private string $mockClassName;




private array $methods;
private bool $callOriginalConstructor;




private array $options;







public function __construct(Telemetry\Info $telemetryInfo, string $wsdlFile, string $originalClassName, string $mockClassName, array $methods, bool $callOriginalConstructor, array $options)
{
$this->telemetryInfo = $telemetryInfo;
$this->wsdlFile = $wsdlFile;
$this->originalClassName = $originalClassName;
$this->mockClassName = $mockClassName;
$this->methods = $methods;
$this->callOriginalConstructor = $callOriginalConstructor;
$this->options = $options;
}

public function telemetryInfo(): Telemetry\Info
{
return $this->telemetryInfo;
}

public function wsdlFile(): string
{
return $this->wsdlFile;
}




public function originalClassName(): string
{
return $this->originalClassName;
}




public function mockClassName(): string
{
return $this->mockClassName;
}




public function methods(): array
{
return $this->methods;
}

public function callOriginalConstructor(): bool
{
return $this->callOriginalConstructor;
}




public function options(): array
{
return $this->options;
}

public function asString(): string
{
return sprintf(
'Mock Object Created (%s)',
$this->wsdlFile,
);
}
}
