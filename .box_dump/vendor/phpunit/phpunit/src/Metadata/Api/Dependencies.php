<?php declare(strict_types=1);








namespace PHPUnit\Metadata\Api;

use function assert;
use PHPUnit\Framework\ExecutionOrderDependency;
use PHPUnit\Metadata\DependsOnClass;
use PHPUnit\Metadata\DependsOnMethod;
use PHPUnit\Metadata\Parser\Registry;

/**
@no-named-arguments


*/
final readonly class Dependencies
{






public static function dependencies(string $className, string $methodName): array
{
$dependencies = [];

foreach (Registry::parser()->forClassAndMethod($className, $methodName)->isDepends() as $metadata) {
if ($metadata->isDependsOnClass()) {
assert($metadata instanceof DependsOnClass);

$dependencies[] = ExecutionOrderDependency::forClass($metadata);

continue;
}

assert($metadata instanceof DependsOnMethod);

if (empty($metadata->methodName())) {
$dependencies[] = ExecutionOrderDependency::invalid();

continue;
}

$dependencies[] = ExecutionOrderDependency::forMethod($metadata);
}

return $dependencies;
}
}
