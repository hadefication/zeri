<?php

declare(strict_types=1);










namespace Carbon\Traits;

use Carbon\CarbonInterface;
use Carbon\CarbonInterval;
use Carbon\CarbonPeriod;
use Closure;
use Generator;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use Throwable;






trait Mixin
{



protected static array $macroContextStack = [];




























public static function mixin(object|string $mixin): void
{
\is_string($mixin) && trait_exists($mixin)
? self::loadMixinTrait($mixin)
: self::loadMixinClass($mixin);
}




private static function loadMixinClass(object|string $mixin): void
{
$methods = (new ReflectionClass($mixin))->getMethods(
ReflectionMethod::IS_PUBLIC | ReflectionMethod::IS_PROTECTED,
);

foreach ($methods as $method) {
if ($method->isConstructor() || $method->isDestructor()) {
continue;
}

$macro = $method->invoke($mixin);

if (\is_callable($macro)) {
static::macro($method->name, $macro);
}
}
}

private static function loadMixinTrait(string $trait): void
{
$context = eval(self::getAnonymousClassCodeForTrait($trait));
$className = \get_class($context);
$baseClass = static::class;

foreach (self::getMixableMethods($context) as $name) {
$closureBase = Closure::fromCallable([$context, $name]);

static::macro($name, function (...$parameters) use ($closureBase, $className, $baseClass) {
$downContext = isset($this) ? ($this) : new $baseClass();
$context = isset($this) ? $this->cast($className) : new $className();

try {

$closure = @$closureBase->bindTo($context);
} catch (Throwable) { 
$closure = $closureBase; 
}


$closure = $closure ?: $closureBase;

$result = $closure(...$parameters);

if (!($result instanceof $className)) {
return $result;
}

if ($downContext instanceof CarbonInterface && $result instanceof CarbonInterface) {
if ($context !== $result) {
$downContext = $downContext->copy();
}

return $downContext
->setTimezone($result->getTimezone())
->modify($result->format('Y-m-d H:i:s.u'))
->settings($result->getSettings());
}

if ($downContext instanceof CarbonInterval && $result instanceof CarbonInterval) {
if ($context !== $result) {
$downContext = $downContext->copy();
}

$downContext->copyProperties($result);
self::copyStep($downContext, $result);
self::copyNegativeUnits($downContext, $result);

return $downContext->settings($result->getSettings());
}

if ($downContext instanceof CarbonPeriod && $result instanceof CarbonPeriod) {
if ($context !== $result) {
$downContext = $downContext->copy();
}

return $downContext
->setDates($result->getStartDate(), $result->getEndDate())
->setRecurrences($result->getRecurrences())
->setOptions($result->getOptions())
->settings($result->getSettings());
}

return $result;
});
}
}

private static function getAnonymousClassCodeForTrait(string $trait): string
{
return 'return new class() extends '.static::class.' {use '.$trait.';};';
}

private static function getMixableMethods(self $context): Generator
{
foreach (get_class_methods($context) as $name) {
if (method_exists(static::class, $name)) {
continue;
}

yield $name;
}
}




protected static function bindMacroContext(?self $context, callable $callable): mixed
{
static::$macroContextStack[] = $context;

try {
return $callable();
} finally {
array_pop(static::$macroContextStack);
}
}




protected static function context(): ?static
{
return end(static::$macroContextStack) ?: null;
}




protected static function this(): static
{
return end(static::$macroContextStack) ?: new static();
}
}
