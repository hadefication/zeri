<?php

namespace Illuminate\Container;

use Closure;
use Illuminate\Contracts\Container\ContextualAttribute;
use ReflectionAttribute;
use ReflectionNamedType;




class Util
{








public static function arrayWrap($value)
{
if (is_null($value)) {
return [];
}

return is_array($value) ? $value : [$value];
}










public static function unwrapIfClosure($value, ...$args)
{
return $value instanceof Closure ? $value(...$args) : $value;
}









public static function getParameterClassName($parameter)
{
$type = $parameter->getType();

if (! $type instanceof ReflectionNamedType || $type->isBuiltin()) {
return null;
}

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







public static function getContextualAttributeFromDependency($dependency)
{
return $dependency->getAttributes(ContextualAttribute::class, ReflectionAttribute::IS_INSTANCEOF)[0] ?? null;
}
}
