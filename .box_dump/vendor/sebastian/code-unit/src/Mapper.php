<?php declare(strict_types=1);








namespace SebastianBergmann\CodeUnit;

use function array_keys;
use function array_merge;
use function array_unique;
use function array_values;
use function class_exists;
use function explode;
use function function_exists;
use function interface_exists;
use function ksort;
use function method_exists;
use function sort;
use function sprintf;
use function str_contains;
use function trait_exists;
use ReflectionClass;
use ReflectionFunction;
use ReflectionMethod;

final class Mapper
{



public function codeUnitsToSourceLines(CodeUnitCollection $codeUnits): array
{
$result = [];

foreach ($codeUnits as $codeUnit) {
$sourceFileName = $codeUnit->sourceFileName();

if (!isset($result[$sourceFileName])) {
$result[$sourceFileName] = [];
}

$result[$sourceFileName] = array_merge($result[$sourceFileName], $codeUnit->sourceLines());
}

foreach (array_keys($result) as $sourceFileName) {
$result[$sourceFileName] = array_values(array_unique($result[$sourceFileName]));

sort($result[$sourceFileName]);
}

ksort($result);

return $result;
}





public function stringToCodeUnits(string $unit): CodeUnitCollection
{
if (str_contains($unit, '::')) {
[$firstPart, $secondPart] = explode('::', $unit);

if ($this->isUserDefinedMethod($firstPart, $secondPart)) {
return CodeUnitCollection::fromList(CodeUnit::forClassMethod($firstPart, $secondPart));
}

if ($this->isUserDefinedFunction($secondPart)) {
return CodeUnitCollection::fromList(CodeUnit::forFunction($secondPart));
}

if ($this->isUserDefinedInterface($firstPart)) {
return CodeUnitCollection::fromList(CodeUnit::forInterfaceMethod($firstPart, $secondPart));
}

if ($this->isUserDefinedTrait($firstPart)) {
return CodeUnitCollection::fromList(CodeUnit::forTraitMethod($firstPart, $secondPart));
}
} else {
if ($this->isUserDefinedClass($unit)) {
return CodeUnitCollection::fromList(
...array_merge(
[CodeUnit::forClass($unit)],
$this->traits(new ReflectionClass($unit)),
),
);
}

if ($this->isUserDefinedInterface($unit)) {
return CodeUnitCollection::fromList(CodeUnit::forInterface($unit));
}

if ($this->isUserDefinedTrait($unit)) {
return CodeUnitCollection::fromList(CodeUnit::forTrait($unit));
}

if ($this->isUserDefinedFunction($unit)) {
return CodeUnitCollection::fromList(CodeUnit::forFunction($unit));
}
}

throw new InvalidCodeUnitException(
sprintf(
'"%s" is not a valid code unit',
$unit,
),
);
}

/**
@phpstan-assert-if-true
*/
private function isUserDefinedFunction(string $functionName): bool
{
if (!function_exists($functionName)) {
return false;
}

return (new ReflectionFunction($functionName))->isUserDefined();
}

/**
@phpstan-assert-if-true
*/
private function isUserDefinedClass(string $className): bool
{
if (!class_exists($className)) {
return false;
}

return (new ReflectionClass($className))->isUserDefined();
}

/**
@phpstan-assert-if-true
*/
private function isUserDefinedInterface(string $interfaceName): bool
{
if (!interface_exists($interfaceName)) {
return false;
}

return (new ReflectionClass($interfaceName))->isUserDefined();
}

/**
@phpstan-assert-if-true
*/
private function isUserDefinedTrait(string $traitName): bool
{
if (!trait_exists($traitName)) {
return false;
}

return (new ReflectionClass($traitName))->isUserDefined();
}

/**
@phpstan-assert-if-true
*/
private function isUserDefinedMethod(string $className, string $methodName): bool
{
if (!class_exists($className)) {
return false;
}

if (!method_exists($className, $methodName)) {
return false;
}

return (new ReflectionMethod($className, $methodName))->isUserDefined();
}






private function traits(ReflectionClass $class): array
{
$result = [];

foreach ($class->getTraits() as $trait) {
if (!$trait->isUserDefined()) {

continue;

}

$result[] = CodeUnit::forTrait($trait->getName());

$result = array_merge($result, $this->traits($trait));
}

return $result;
}
}
