<?php declare(strict_types=1);








namespace PHPUnit\Util;

use function array_keys;
use function array_merge;
use function array_reverse;
use function assert;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;

/**
@no-named-arguments


*/
final readonly class Reflection
{






public static function sourceLocationFor(string $className, string $methodName): array
{
try {
$reflector = new ReflectionMethod($className, $methodName);

$file = $reflector->getFileName();
$line = $reflector->getStartLine();
} catch (ReflectionException) {
$file = 'unknown';
$line = 0;
}

assert($file !== false && $file !== '');
assert($line !== false && $line >= 0);

return [
'file' => $file,
'line' => $line,
];
}






public static function publicMethodsDeclaredDirectlyInTestClass(ReflectionClass $class): array
{
return self::filterAndSortMethods($class, ReflectionMethod::IS_PUBLIC, true);
}






public static function methodsDeclaredDirectlyInTestClass(ReflectionClass $class): array
{
return self::filterAndSortMethods($class, null, false);
}






private static function filterAndSortMethods(ReflectionClass $class, ?int $filter, bool $sortHighestToLowest): array
{
$methodsByClass = [];

foreach ($class->getMethods($filter) as $method) {
$declaringClassName = $method->getDeclaringClass()->getName();

if ($declaringClassName === TestCase::class) {
continue;
}

if ($declaringClassName === Assert::class) {
continue;
}

if (!isset($methodsByClass[$declaringClassName])) {
$methodsByClass[$declaringClassName] = [];
}

$methodsByClass[$declaringClassName][] = $method;
}

$classNames = array_keys($methodsByClass);

if ($sortHighestToLowest) {
$classNames = array_reverse($classNames);
}

$methods = [];

foreach ($classNames as $className) {
$methods = array_merge($methods, $methodsByClass[$className]);
}

return $methods;
}
}
