<?php declare(strict_types=1);








namespace PHPUnit\Framework\MockObject\Generator;

use function class_exists;
use PHPUnit\Framework\MockObject\ConfigurableMethod;

/**
@no-named-arguments


*/
final readonly class MockClass implements MockType
{
private string $classCode;




private string $mockName;




private array $configurableMethods;





public function __construct(string $classCode, string $mockName, array $configurableMethods)
{
$this->classCode = $classCode;
$this->mockName = $mockName;
$this->configurableMethods = $configurableMethods;
}




public function generate(): string
{
if (!class_exists($this->mockName, false)) {
eval($this->classCode);
}

return $this->mockName;
}

public function classCode(): string
{
return $this->classCode;
}




public function configurableMethods(): array
{
return $this->configurableMethods;
}
}
