<?php declare(strict_types=1);








namespace PHPUnit\Event\Test;

use const PHP_EOL;
use function sprintf;
use PHPUnit\Event\Code;
use PHPUnit\Event\Event;
use PHPUnit\Event\Telemetry;

/**
@immutable
@no-named-arguments

*/
final readonly class AfterLastTestMethodFinished implements Event
{
private Telemetry\Info $telemetryInfo;




private string $testClassName;




private array $calledMethods;




public function __construct(Telemetry\Info $telemetryInfo, string $testClassName, Code\ClassMethod ...$calledMethods)
{
$this->telemetryInfo = $telemetryInfo;
$this->testClassName = $testClassName;
$this->calledMethods = $calledMethods;
}

public function telemetryInfo(): Telemetry\Info
{
return $this->telemetryInfo;
}




public function testClassName(): string
{
return $this->testClassName;
}




public function calledMethods(): array
{
return $this->calledMethods;
}

public function asString(): string
{
$buffer = 'After Last Test Method Finished:';

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
