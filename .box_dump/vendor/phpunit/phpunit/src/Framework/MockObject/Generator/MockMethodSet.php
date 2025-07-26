<?php declare(strict_types=1);








namespace PHPUnit\Framework\MockObject\Generator;

use function array_key_exists;
use function array_values;
use function strtolower;

/**
@no-named-arguments


*/
final class MockMethodSet
{



private array $methods = [];

public function addMethods(MockMethod ...$methods): void
{
foreach ($methods as $method) {
$this->methods[strtolower($method->methodName())] = $method;
}
}




public function asArray(): array
{
return array_values($this->methods);
}

public function hasMethod(string $methodName): bool
{
return array_key_exists(strtolower($methodName), $this->methods);
}
}
