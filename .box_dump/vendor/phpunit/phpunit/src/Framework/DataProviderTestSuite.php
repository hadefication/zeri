<?php declare(strict_types=1);








namespace PHPUnit\Framework;

use function assert;
use function class_exists;
use function explode;
use PHPUnit\Framework\TestSize\TestSize;
use PHPUnit\Metadata\Api\Groups;

/**
@no-named-arguments


*/
final class DataProviderTestSuite extends TestSuite
{



private array $dependencies = [];




private ?array $providedTests = null;




public function setDependencies(array $dependencies): void
{
$this->dependencies = $dependencies;

foreach ($this->tests() as $test) {
if (!$test instanceof TestCase) {
continue;
}

$test->setDependencies($dependencies);
}
}




public function provides(): array
{
if ($this->providedTests === null) {
$this->providedTests = [new ExecutionOrderDependency($this->name())];
}

return $this->providedTests;
}




public function requires(): array
{


return $this->dependencies;
}




public function size(): TestSize
{
[$className, $methodName] = explode('::', $this->name());

assert(class_exists($className));
assert($methodName !== '');

return (new Groups)->size($className, $methodName);
}
}
