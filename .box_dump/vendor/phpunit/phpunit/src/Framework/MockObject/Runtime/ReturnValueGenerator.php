<?php declare(strict_types=1);








namespace PHPUnit\Framework\MockObject;

use function array_keys;
use function array_map;
use function explode;
use function in_array;
use function interface_exists;
use function sprintf;
use function str_contains;
use function str_ends_with;
use function str_starts_with;
use function substr;
use PHPUnit\Framework\MockObject\Generator\Generator;
use ReflectionClass;
use ReflectionObject;
use stdClass;
use Throwable;

/**
@no-named-arguments


*/
final class ReturnValueGenerator
{






public function generate(string $className, string $methodName, StubInternal $testStub, string $returnType): mixed
{
$intersection = false;
$union = false;

if (str_contains($returnType, '|')) {
$types = explode('|', $returnType);
$union = true;

foreach (array_keys($types) as $key) {
if (str_starts_with($types[$key], '(') && str_ends_with($types[$key], ')')) {
$types[$key] = substr($types[$key], 1, -1);
}
}
} elseif (str_contains($returnType, '&')) {
$types = explode('&', $returnType);
$intersection = true;
} else {
$types = [$returnType];
}

if (!$intersection) {
$lowerTypes = array_map('strtolower', $types);

if (in_array('', $lowerTypes, true) ||
in_array('null', $lowerTypes, true) ||
in_array('mixed', $lowerTypes, true) ||
in_array('void', $lowerTypes, true)) {
return null;
}

if (in_array('true', $lowerTypes, true)) {
return true;
}

if (in_array('false', $lowerTypes, true) ||
in_array('bool', $lowerTypes, true)) {
return false;
}

if (in_array('float', $lowerTypes, true)) {
return 0.0;
}

if (in_array('int', $lowerTypes, true)) {
return 0;
}

if (in_array('string', $lowerTypes, true)) {
return '';
}

if (in_array('array', $lowerTypes, true)) {
return [];
}

if (in_array('static', $lowerTypes, true)) {
return $this->newInstanceOf($testStub, $className, $methodName);
}

if (in_array('object', $lowerTypes, true)) {
return new stdClass;
}

if (in_array('callable', $lowerTypes, true) ||
in_array('closure', $lowerTypes, true)) {
return static function (): void
{
};
}

if (in_array('traversable', $lowerTypes, true) ||
in_array('generator', $lowerTypes, true) ||
in_array('iterable', $lowerTypes, true)) {
$generator = static function (): \Generator
{
yield from [];
};

return $generator();
}

if (!$union) {
return $this->testDoubleFor($returnType, $className, $methodName);
}
}

if ($union) {
foreach ($types as $type) {
if (str_contains($type, '&')) {
$_types = explode('&', $type);

if ($this->onlyInterfaces($_types)) {
return $this->testDoubleForIntersectionOfInterfaces($_types, $className, $methodName);
}
}
}
}

if ($intersection && $this->onlyInterfaces($types)) {
return $this->testDoubleForIntersectionOfInterfaces($types, $className, $methodName);
}

$reason = '';

if ($union) {
$reason = ' because the declared return type is a union';
} elseif ($intersection) {
$reason = ' because the declared return type is an intersection';
}

throw new RuntimeException(
sprintf(
'Return value for %s::%s() cannot be generated%s, please configure a return value for this method',
$className,
$methodName,
$reason,
),
);
}




private function onlyInterfaces(array $types): bool
{
foreach ($types as $type) {
if (!interface_exists($type)) {
return false;
}
}

return true;
}







private function newInstanceOf(StubInternal $testStub, string $className, string $methodName): Stub
{
try {
$object = (new ReflectionClass($testStub::class))->newInstanceWithoutConstructor();
$reflector = new ReflectionObject($object);

$reflector->getProperty('__phpunit_state')->setValue(
$object,
new TestDoubleState(
$testStub->__phpunit_state()->configurableMethods(),
$testStub->__phpunit_state()->generateReturnValues(),
),
);

return $object;

} catch (Throwable $t) {
throw new RuntimeException(
sprintf(
'Return value for %s::%s() cannot be generated: %s',
$className,
$methodName,
$t->getMessage(),
),
);

}
}








private function testDoubleFor(string $type, string $className, string $methodName): Stub
{
try {
return (new Generator)->testDouble($type, false, false, [], [], '', false);

} catch (Throwable $t) {
throw new RuntimeException(
sprintf(
'Return value for %s::%s() cannot be generated: %s',
$className,
$methodName,
$t->getMessage(),
),
);

}
}








private function testDoubleForIntersectionOfInterfaces(array $types, string $className, string $methodName): Stub
{
try {
return (new Generator)->testDoubleForInterfaceIntersection($types, false);

} catch (Throwable $t) {
throw new RuntimeException(
sprintf(
'Return value for %s::%s() cannot be generated: %s',
$className,
$methodName,
$t->getMessage(),
),
);

}
}
}
