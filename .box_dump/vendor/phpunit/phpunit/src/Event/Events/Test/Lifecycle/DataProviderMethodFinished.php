<?php declare(strict_types=1);








namespace PHPUnit\Event\Test;

use const PHP_EOL;
use function sprintf;
use PHPUnit\Event\Code\ClassMethod;
use PHPUnit\Event\Event;
use PHPUnit\Event\Telemetry;

/**
@immutable
@no-named-arguments

*/
final readonly class DataProviderMethodFinished implements Event
{
private Telemetry\Info $telemetryInfo;
private ClassMethod $testMethod;




private array $calledMethods;

public function __construct(Telemetry\Info $telemetryInfo, ClassMethod $testMethod, ClassMethod ...$calledMethods)
{
$this->telemetryInfo = $telemetryInfo;
$this->testMethod = $testMethod;
$this->calledMethods = $calledMethods;
}

public function telemetryInfo(): Telemetry\Info
{
return $this->telemetryInfo;
}

public function testMethod(): ClassMethod
{
return $this->testMethod;
}




public function calledMethods(): array
{
return $this->calledMethods;
}

public function asString(): string
{
$buffer = sprintf(
'Data Provider Method Finished for %s::%s:',
$this->testMethod->className(),
$this->testMethod->methodName(),
);

foreach ($this->calledMethods as $calledMethod) {
$buffer .= sprintf(
PHP_EOL . '- %s::%s',
$calledMethod->className(),
$calledMethod->methodName(),
);
}

return $buffer;
}
}
