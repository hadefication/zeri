<?php

declare(strict_types=1);

namespace Pest\Support;

use Closure;
use InvalidArgumentException;
use Pest\Exceptions\ShouldNotHappen;
use Pest\TestSuite;
use ReflectionClass;
use ReflectionException;
use ReflectionFunction;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionProperty;
use ReflectionUnionType;




final class Reflection
{





public static function call(object $object, string $method, array $args = []): mixed
{
$reflectionClass = new ReflectionClass($object);

try {
$reflectionMethod = $reflectionClass->getMethod($method);

$reflectionMethod->setAccessible(true);

return $reflectionMethod->invoke($object, ...$args);
} catch (ReflectionException $exception) {
if (method_exists($object, '__call')) {
return $object->__call($method, $args);
}

if (is_callable($method)) {
return self::bindCallable($method, $args);
}

throw $exception;
}
}






public static function bindCallable(callable $callable, array $args = []): mixed
{
return Closure::fromCallable($callable)->bindTo(TestSuite::getInstance()->test)(...$args);
}





public static function bindCallableWithData(callable $callable): mixed
{
$test = TestSuite::getInstance()->test;

if (! $test instanceof \PHPUnit\Framework\TestCase) {
return self::bindCallable($callable);
}

foreach ($test->providedData() as $value) {
if ($value instanceof Closure) {
throw new InvalidArgumentException('Bound datasets are not supported while doing high order testing.');
}
}

return Closure::fromCallable($callable)->bindTo($test)(...$test->providedData());
}




public static function getFileNameFromClosure(Closure $closure): string
{
$reflectionClosure = new ReflectionFunction($closure);

return (string) $reflectionClosure->getFileName();
}




public static function getPropertyValue(object $object, string $property): mixed
{
$reflectionClass = new ReflectionClass($object);

$reflectionProperty = null;

while (! $reflectionProperty instanceof ReflectionProperty) {
try {

$reflectionProperty = $reflectionClass->getProperty($property);
} catch (ReflectionException $reflectionException) {
$reflectionClass = $reflectionClass->getParentClass();

if (! $reflectionClass instanceof ReflectionClass) {
throw new ShouldNotHappen($reflectionException);
}
}
}

$reflectionProperty->setAccessible(true);

return $reflectionProperty->getValue($object);
}

/**
@template




*/
public static function setPropertyValue(object $object, string $property, mixed $value): void
{

$reflectionClass = new ReflectionClass($object);

$reflectionProperty = null;

while (! $reflectionProperty instanceof ReflectionProperty) {
try {

$reflectionProperty = $reflectionClass->getProperty($property);
} catch (ReflectionException $reflectionException) {
$reflectionClass = $reflectionClass->getParentClass();

if (! $reflectionClass instanceof ReflectionClass) {
throw new ShouldNotHappen($reflectionException);
}
}
}

$reflectionProperty->setAccessible(true);
$reflectionProperty->setValue($object, $value);
}






public static function getParameterClassName(ReflectionParameter $parameter): ?string
{
$type = $parameter->getType();
if (! $type instanceof ReflectionNamedType) {
return null;
}
if ($type->isBuiltin()) {
return null;
}

$name = $type->getName();

if (($class = $parameter->getDeclaringClass()) instanceof ReflectionClass) {
if ($name === 'self') {
return $class->getName();
}

if ($name === 'parent' && ($parent = $class->getParentClass()) instanceof ReflectionClass) {
return $parent->getName();
}
}

return $name;
}






public static function getFunctionArguments(Closure $function): array
{
$parameters = (new ReflectionFunction($function))->getParameters();
$arguments = [];

foreach ($parameters as $parameter) {

$types = ($parameter->hasType()) ? $parameter->getType() : null;

if (is_null($types)) {
$arguments[$parameter->getName()] = 'mixed';

continue;
}

$arguments[$parameter->getName()] = implode('|', array_map(
static fn (ReflectionNamedType $type): string => $type->getName(), 
($types instanceof ReflectionNamedType)
? [$types] 
: $types->getTypes(),
));
}

return $arguments;
}

public static function getFunctionVariable(Closure $function, string $key): mixed
{
return (new ReflectionFunction($function))->getStaticVariables()[$key] ?? null;
}









public static function getPropertiesFromReflectionClass(ReflectionClass $reflectionClass): array
{
$getProperties = fn (ReflectionClass $reflectionClass): array => array_filter(
array_map(
fn (ReflectionProperty $property): \ReflectionProperty => $property,
$reflectionClass->getProperties(),
), fn (ReflectionProperty $property): bool => $property->getDeclaringClass()->getName() === $reflectionClass->getName(),
);

$propertiesFromTraits = [];
foreach ($reflectionClass->getTraits() as $trait) {
$propertiesFromTraits = array_merge($propertiesFromTraits, $getProperties($trait));
}

$propertiesFromTraits = array_map(
fn (ReflectionProperty $property): string => $property->getName(),
$propertiesFromTraits,
);

return array_values(
array_filter(
$getProperties($reflectionClass),
fn (ReflectionProperty $property): bool => ! in_array($property->getName(), $propertiesFromTraits, true),
),
);
}









public static function getMethodsFromReflectionClass(ReflectionClass $reflectionClass, int $filter = ReflectionMethod::IS_PUBLIC | ReflectionMethod::IS_PROTECTED | ReflectionMethod::IS_PRIVATE): array
{
$getMethods = fn (ReflectionClass $reflectionClass): array => array_filter(
array_map(
fn (ReflectionMethod $method): \ReflectionMethod => $method,
$reflectionClass->getMethods($filter),
), fn (ReflectionMethod $method): bool => $method->getDeclaringClass()->getName() === $reflectionClass->getName(),
);

$methodsFromTraits = [];
foreach ($reflectionClass->getTraits() as $trait) {
$methodsFromTraits = array_merge($methodsFromTraits, $getMethods($trait));
}

$methodsFromTraits = array_map(
fn (ReflectionMethod $method): string => $method->getName(),
$methodsFromTraits,
);

return array_values(
array_filter(
$getMethods($reflectionClass),
fn (ReflectionMethod $method): bool => ! in_array($method->getName(), $methodsFromTraits, true),
),
);
}
}
