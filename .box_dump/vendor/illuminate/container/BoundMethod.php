<?php

namespace Illuminate\Container;

use Closure;
use Illuminate\Contracts\Container\BindingResolutionException;
use InvalidArgumentException;
use ReflectionFunction;
use ReflectionMethod;

class BoundMethod
{












public static function call($container, $callback, array $parameters = [], $defaultMethod = null)
{
if (is_string($callback) && ! $defaultMethod && method_exists($callback, '__invoke')) {
$defaultMethod = '__invoke';
}

if (static::isCallableWithAtSign($callback) || $defaultMethod) {
return static::callClass($container, $callback, $parameters, $defaultMethod);
}

return static::callBoundMethod($container, $callback, function () use ($container, $callback, $parameters) {
return $callback(...array_values(static::getMethodDependencies($container, $callback, $parameters)));
});
}












protected static function callClass($container, $target, array $parameters = [], $defaultMethod = null)
{
$segments = explode('@', $target);




$method = count($segments) === 2
? $segments[1]
: $defaultMethod;

if (is_null($method)) {
throw new InvalidArgumentException('Method not provided.');
}

return static::call(
$container,
[$container->make($segments[0]), $method],
$parameters
);
}









protected static function callBoundMethod($container, $callback, $default)
{
if (! is_array($callback)) {
return Util::unwrapIfClosure($default);
}




$method = static::normalizeMethod($callback);

if ($container->hasMethodBinding($method)) {
return $container->callMethodBinding($method, $callback[0]);
}

return Util::unwrapIfClosure($default);
}







protected static function normalizeMethod($callback)
{
$class = is_string($callback[0]) ? $callback[0] : get_class($callback[0]);

return "{$class}@{$callback[1]}";
}











protected static function getMethodDependencies($container, $callback, array $parameters = [])
{
$dependencies = [];

foreach (static::getCallReflector($callback)->getParameters() as $parameter) {
static::addDependencyForCallParameter($container, $parameter, $parameters, $dependencies);
}

return array_merge($dependencies, array_values($parameters));
}









protected static function getCallReflector($callback)
{
if (is_string($callback) && str_contains($callback, '::')) {
$callback = explode('::', $callback);
} elseif (is_object($callback) && ! $callback instanceof Closure) {
$callback = [$callback, '__invoke'];
}

return is_array($callback)
? new ReflectionMethod($callback[0], $callback[1])
: new ReflectionFunction($callback);
}












protected static function addDependencyForCallParameter(
$container,
$parameter,
array &$parameters,
&$dependencies
) {
$pendingDependencies = [];

if (array_key_exists($paramName = $parameter->getName(), $parameters)) {
$pendingDependencies[] = $parameters[$paramName];

unset($parameters[$paramName]);
} elseif ($attribute = Util::getContextualAttributeFromDependency($parameter)) {
$pendingDependencies[] = $container->resolveFromAttribute($attribute);
} elseif (! is_null($className = Util::getParameterClassName($parameter))) {
if (array_key_exists($className, $parameters)) {
$pendingDependencies[] = $parameters[$className];

unset($parameters[$className]);
} elseif ($parameter->isVariadic()) {
$variadicDependencies = $container->make($className);

$pendingDependencies = array_merge($pendingDependencies, is_array($variadicDependencies)
? $variadicDependencies
: [$variadicDependencies]);
} else {
$pendingDependencies[] = $container->make($className);
}
} elseif ($parameter->isDefaultValueAvailable()) {
$pendingDependencies[] = $parameter->getDefaultValue();
} elseif (! $parameter->isOptional() && ! array_key_exists($paramName, $parameters)) {
$message = "Unable to resolve dependency [{$parameter}] in class {$parameter->getDeclaringClass()->getName()}";

throw new BindingResolutionException($message);
}

foreach ($pendingDependencies as $dependency) {
$container->fireAfterResolvingAttributeCallbacks($parameter->getAttributes(), $dependency);
}

$dependencies = array_merge($dependencies, $pendingDependencies);
}







protected static function isCallableWithAtSign($callback)
{
return is_string($callback) && str_contains($callback, '@');
}
}
