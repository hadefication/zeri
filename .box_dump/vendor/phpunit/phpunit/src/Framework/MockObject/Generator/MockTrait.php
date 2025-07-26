<?php declare(strict_types=1);








namespace PHPUnit\Framework\MockObject\Generator;

use function class_exists;

/**
@no-named-arguments




*/
final readonly class MockTrait implements MockType
{
private string $classCode;




private string $mockName;




public function __construct(string $classCode, string $mockName)
{
$this->classCode = $classCode;
$this->mockName = $mockName;
}




public function generate(): string
{
if (!class_exists($this->mockName, false)) {
eval($this->classCode);
}

return $this->mockName;
}
}
