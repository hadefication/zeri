<?php

namespace Illuminate\Support;

use ReflectionAttribute;
use ReflectionClass;
use ReflectionEnum;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionUnionType;

class Reflector
{







public static function isCallable($var, $syntaxOnly = false)
{
if (! is_array($var)) {
return is_callable($var, $syntaxOnly);
}

if (! isset($var[0], $var[1]) || ! is_string($var[1] ?? null)) {
return false;
}

if ($syntaxOnly &&
(is_string($var[0]) || is_object($var[0])) &&
is_string($var[1])) {
return true;
}

$class = is_object($var[0]) ? get_class($var[0]) : $var[0];

$method = $var[1];

if (! class_exists($class)) {
return false;
}

if (method_exists($class, $method)) {
return (new ReflectionMethod($class, $method))->isPublic();
}

if (is_object($var[0]) && method_exists($class, '__call')) {
return (new ReflectionMethod($class, '__call'))->isPublic();
}

if (! is_object($var[0]) && method_exists($class, '__callStatic')) {
return (new ReflectionMethod($class, '__callStatic'))->isPublic();
}

return false;
}

/**
@template






*/
public static function getClassAttribute($objectOrClass, $attribute, $ascend = false)
{
return static::getClassAttributes($objectOrClass, $attribute, $ascend)->flatten()->first();
}

/**
@template
@template






*/
public static function getClassAttributes($objectOrClass, $attribute, $includeParents = false)
{
$reflectionClass = new ReflectionClass($objectOrClass);

$attributes = [];

do {
$attributes[$reflectionClass->name] = new Collection(array_map(
fn (ReflectionAttribute $reflectionAttribute) => $reflectionAttribute->newInstance(),
$reflectionClass->getAttributes($attribute)
));
} while ($includeParents && false !== $reflectionClass = $reflectionClass->getParentClass());

return $includeParents ? new Collection($attributes) : reset($attributes);
}







public static function getParameterClassName($parameter)
{
$type = $parameter->getType();

if (! $type instanceof ReflectionNamedType || $type->isBuiltin()) {
return;
}

return static::getTypeName($parameter, $type);
}







public static function getParameterClassNames($parameter)
{
$type = $parameter->getType();

if (! $type instanceof ReflectionUnionType) {
return array_filter([static::getParameterClassName($parameter)]);
}

$unionTypes = [];

foreach ($type->getTypes() as $listedType) {
if (! $listedType instanceof ReflectionNamedType || $listedType->isBuiltin()) {
continue;
}

$unionTypes[] = static::getTypeName($parameter, $listedType);
}

return array_filter($unionTypes);
}








protected static function getTypeName($parameter, $type)
{
$name = $type->getName();

if (! is_null($class = $parameter->getDeclaringClass())) {
if ($name === 'self') {
return $class->getName();
}

if ($name === 'parent' && $parent = $class->getParentClass()) {
return $parent->getName();
}
}

return $name;
}








public static function isParameterSubclassOf($parameter, $className)
{
$paramClassName = static::getParameterClassName($parameter);

return $paramClassName
&& (class_exists($paramClassName) || interface_exists($paramClassName))
&& (new ReflectionClass($paramClassName))->isSubclassOf($className);
}







public static function isParameterBackedEnumWithStringBackingType($parameter)
{
if (! $parameter->getType() instanceof ReflectionNamedType) {
return false;
}

$backedEnumClass = $parameter->getType()?->getName();

if (is_null($backedEnumClass)) {
return false;
}

if (enum_exists($backedEnumClass)) {
$reflectionBackedEnum = new ReflectionEnum($backedEnumClass);

return $reflectionBackedEnum->isBacked()
&& $reflectionBackedEnum->getBackingType()->getName() == 'string';
}

return false;
}
}
