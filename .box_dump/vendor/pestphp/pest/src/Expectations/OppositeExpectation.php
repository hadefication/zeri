<?php

declare(strict_types=1);

namespace Pest\Expectations;

use Attribute;
use Pest\Arch\Contracts\ArchExpectation;
use Pest\Arch\Expectations\Targeted;
use Pest\Arch\Expectations\ToBeUsedIn;
use Pest\Arch\Expectations\ToBeUsedInNothing;
use Pest\Arch\Expectations\ToUse;
use Pest\Arch\GroupArchExpectation;
use Pest\Arch\PendingArchExpectation;
use Pest\Arch\SingleArchExpectation;
use Pest\Arch\Support\FileLineFinder;
use Pest\Exceptions\InvalidExpectation;
use Pest\Expectation;
use Pest\Support\Arr;
use Pest\Support\Exporter;
use Pest\Support\Reflection;
use PHPUnit\Architecture\Elements\ObjectDescription;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\ExpectationFailedException;
use ReflectionMethod;
use ReflectionProperty;
use stdClass;

/**
@template
@mixin



*/
final readonly class OppositeExpectation
{





public function __construct(private Expectation $original) {}







public function toHaveKeys(array $keys): Expectation
{
foreach ($keys as $k => $key) {
try {
if (is_array($key)) {
$this->toHaveKeys(array_keys(Arr::dot($key, $k.'.')));
} else {
$this->original->toHaveKey($key);
}
} catch (ExpectationFailedException) {
continue;
}

$this->throwExpectationFailedException('toHaveKey', [$key]);
}

return $this->original;
}






public function toUse(array|string $targets): ArchExpectation
{

$original = $this->original;

return GroupArchExpectation::fromExpectations($original, array_map(fn (string $target): SingleArchExpectation => ToUse::make($original, $target)->opposite(
fn () => $this->throwExpectationFailedException('toUse', $target),
), is_string($targets) ? [$targets] : $targets));
}




public function toHaveFileSystemPermissions(string $permissions): ArchExpectation
{

$original = $this->original;

return Targeted::make(
$original,
fn (ObjectDescription $object): bool => substr(sprintf('%o', fileperms($object->path)), -4) !== $permissions,
sprintf('permissions not to be [%s]', $permissions),
FileLineFinder::where(fn (string $line): bool => str_contains($line, '<?php')),
);
}




public function toHaveLineCountLessThan(): ArchExpectation
{
throw InvalidExpectation::fromMethods(['not', 'toHaveLineCountLessThan']);
}




public function toHaveMethodsDocumented(): ArchExpectation
{

$original = $this->original;

return Targeted::make(
$original,
fn (ObjectDescription $object): bool => isset($object->reflectionClass) === false
|| array_filter(
Reflection::getMethodsFromReflectionClass($object->reflectionClass),
fn (ReflectionMethod $method): bool => (enum_exists($object->name) === false || in_array($method->name, ['from', 'tryFrom', 'cases'], true) === false)
&& realpath($method->getFileName() ?: '/') === realpath($object->path) 
&& $method->getDocComment() !== false,
) === [],
'to have methods without documentation / annotations',
FileLineFinder::where(fn (string $line): bool => str_contains($line, 'class'))
);
}




public function toHavePropertiesDocumented(): ArchExpectation
{

$original = $this->original;

return Targeted::make(
$original,
fn (ObjectDescription $object): bool => isset($object->reflectionClass) === false
|| array_filter(
Reflection::getPropertiesFromReflectionClass($object->reflectionClass),
fn (ReflectionProperty $property): bool => (enum_exists($object->name) === false || in_array($property->name, ['value', 'name'], true) === false)
&& realpath($property->getDeclaringClass()->getFileName() ?: '/') === realpath($object->path) 
&& $property->isPromoted() === false
&& $property->getDocComment() !== false,
) === [],
'to have properties without documentation / annotations',
FileLineFinder::where(fn (string $line): bool => str_contains($line, 'class'))
);
}




public function toUseStrictTypes(): ArchExpectation
{

$original = $this->original;

return Targeted::make(
$original,
fn (ObjectDescription $object): bool => ! (bool) preg_match('/^<\?php\s+declare\(.*?strict_types\s?=\s?1.*?\);/', (string) file_get_contents($object->path)),
'not to use strict types',
FileLineFinder::where(fn (string $line): bool => str_contains($line, '<?php')),
);
}




public function toUseStrictEquality(): ArchExpectation
{

$original = $this->original;

return Targeted::make(
$original,
fn (ObjectDescription $object): bool => ! str_contains((string) file_get_contents($object->path), ' === ') && ! str_contains((string) file_get_contents($object->path), ' !== '),
'to use strict equality',
FileLineFinder::where(fn (string $line): bool => str_contains($line, ' === ') || str_contains($line, ' !== ')),
);
}




public function toBeFinal(): ArchExpectation
{

$original = $this->original;

return Targeted::make(
$original,
fn (ObjectDescription $object): bool => ! enum_exists($object->name) && (isset($object->reflectionClass) === false || ! $object->reflectionClass->isFinal()),
'not to be final',
FileLineFinder::where(fn (string $line): bool => str_contains($line, 'class')),
);
}




public function toBeReadonly(): ArchExpectation
{

$original = $this->original;

return Targeted::make(
$original,
fn (ObjectDescription $object): bool => ! enum_exists($object->name) && (isset($object->reflectionClass) === false || ! $object->reflectionClass->isReadOnly()) && assert(true), 
'not to be readonly',
FileLineFinder::where(fn (string $line): bool => str_contains($line, 'class')),
);
}




public function toBeTrait(): ArchExpectation
{

$original = $this->original;

return Targeted::make(
$original,
fn (ObjectDescription $object): bool => isset($object->reflectionClass) === false || ! $object->reflectionClass->isTrait(),
'not to be trait',
FileLineFinder::where(fn (string $line): bool => str_contains($line, 'class')),
);
}




public function toBeTraits(): ArchExpectation
{
return $this->toBeTrait();
}




public function toBeAbstract(): ArchExpectation
{

$original = $this->original;

return Targeted::make(
$original,
fn (ObjectDescription $object): bool => isset($object->reflectionClass) === false || ! $object->reflectionClass->isAbstract(),
'not to be abstract',
FileLineFinder::where(fn (string $line): bool => str_contains($line, 'class')),
);
}






public function toHaveMethod(array|string $method): ArchExpectation
{
$methods = is_array($method) ? $method : [$method];


$original = $this->original;

return Targeted::make(
$original,
fn (ObjectDescription $object): bool => array_filter(
$methods,
fn (string $method): bool => isset($object->reflectionClass) === false || $object->reflectionClass->hasMethod($method),
) === [],
'to not have methods: '.implode(', ', $methods),
FileLineFinder::where(fn (string $line): bool => str_contains($line, 'class')),
);
}






public function toHaveMethods(array $methods): ArchExpectation
{
return $this->toHaveMethod($methods);
}






public function toHavePublicMethodsBesides(array|string $methods): ArchExpectation
{
$methods = is_array($methods) ? $methods : [$methods];

$state = new stdClass;


$original = $this->original;

return Targeted::make(
$original,
function (ObjectDescription $object) use ($methods, &$state): bool {
$reflectionMethods = isset($object->reflectionClass)
? Reflection::getMethodsFromReflectionClass($object->reflectionClass, ReflectionMethod::IS_PUBLIC)
: [];

foreach ($reflectionMethods as $reflectionMethod) {
if (! in_array($reflectionMethod->name, $methods, true)) {
$state->contains = 'public function '.$reflectionMethod->name;

return false;
}
}

return true;
},
$methods === []
? 'not to have public methods'
: sprintf("not to have public methods besides '%s'", implode("', '", $methods)),
FileLineFinder::where(fn (string $line): bool => str_contains($line, (string) $state->contains)),
);
}




public function toHavePublicMethods(): ArchExpectation
{
return $this->toHavePublicMethodsBesides([]);
}






public function toHaveProtectedMethodsBesides(array|string $methods): ArchExpectation
{
$methods = is_array($methods) ? $methods : [$methods];

$state = new stdClass;


$original = $this->original;

return Targeted::make(
$original,
function (ObjectDescription $object) use ($methods, &$state): bool {
$reflectionMethods = isset($object->reflectionClass)
? Reflection::getMethodsFromReflectionClass($object->reflectionClass, ReflectionMethod::IS_PROTECTED)
: [];

foreach ($reflectionMethods as $reflectionMethod) {
if (! in_array($reflectionMethod->name, $methods, true)) {
$state->contains = 'protected function '.$reflectionMethod->name;

return false;
}
}

return true;
},
$methods === []
? 'not to have protected methods'
: sprintf("not to have protected methods besides '%s'", implode("', '", $methods)),
FileLineFinder::where(fn (string $line): bool => str_contains($line, (string) $state->contains)),
);
}




public function toHaveProtectedMethods(): ArchExpectation
{
return $this->toHaveProtectedMethodsBesides([]);
}






public function toHavePrivateMethodsBesides(array|string $methods): ArchExpectation
{
$methods = is_array($methods) ? $methods : [$methods];

$state = new stdClass;


$original = $this->original;

return Targeted::make(
$original,
function (ObjectDescription $object) use ($methods, &$state): bool {
$reflectionMethods = isset($object->reflectionClass)
? Reflection::getMethodsFromReflectionClass($object->reflectionClass, ReflectionMethod::IS_PRIVATE)
: [];

foreach ($reflectionMethods as $reflectionMethod) {
if (! in_array($reflectionMethod->name, $methods, true)) {
$state->contains = 'private function '.$reflectionMethod->name;

return false;
}
}

return true;
},
$methods === []
? 'not to have private methods'
: sprintf("not to have private methods besides '%s'", implode("', '", $methods)),
FileLineFinder::where(fn (string $line): bool => str_contains($line, (string) $state->contains)),
);
}




public function toHavePrivateMethods(): ArchExpectation
{
return $this->toHavePrivateMethodsBesides([]);
}




public function toBeEnum(): ArchExpectation
{

$original = $this->original;

return Targeted::make(
$original,
fn (ObjectDescription $object): bool => isset($object->reflectionClass) === false || ! $object->reflectionClass->isEnum(),
'not to be enum',
FileLineFinder::where(fn (string $line): bool => str_contains($line, 'class')),
);
}




public function toBeEnums(): ArchExpectation
{
return $this->toBeEnum();
}




public function toBeClass(): ArchExpectation
{

$original = $this->original;

return Targeted::make(
$original,
fn (ObjectDescription $object): bool => ! class_exists($object->name),
'not to be class',
FileLineFinder::where(fn (string $line): bool => true),
);
}




public function toBeClasses(): ArchExpectation
{
return $this->toBeClass();
}




public function toBeInterface(): ArchExpectation
{

$original = $this->original;

return Targeted::make(
$original,
fn (ObjectDescription $object): bool => isset($object->reflectionClass) === false || ! $object->reflectionClass->isInterface(),
'not to be interface',
FileLineFinder::where(fn (string $line): bool => str_contains($line, 'class')),
);
}




public function toBeInterfaces(): ArchExpectation
{
return $this->toBeInterface();
}




public function toExtend(string $class): ArchExpectation
{

$original = $this->original;

return Targeted::make(
$original,
fn (ObjectDescription $object): bool => isset($object->reflectionClass) === false || ! $object->reflectionClass->isSubclassOf($class),
sprintf("not to extend '%s'", $class),
FileLineFinder::where(fn (string $line): bool => str_contains($line, 'class')),
);
}




public function toExtendNothing(): ArchExpectation
{

$original = $this->original;

return Targeted::make(
$original,
fn (ObjectDescription $object): bool => isset($object->reflectionClass) === false || $object->reflectionClass->getParentClass() !== false,
'to extend a class',
FileLineFinder::where(fn (string $line): bool => str_contains($line, 'class')),
);
}




public function toUseTrait(string $trait): ArchExpectation
{
return $this->toUseTraits($trait);
}






public function toUseTraits(array|string $traits): ArchExpectation
{
$traits = is_array($traits) ? $traits : [$traits];


$original = $this->original;

return Targeted::make(
$original,
function (ObjectDescription $object) use ($traits): bool {
foreach ($traits as $trait) {
if (isset($object->reflectionClass) && in_array($trait, $object->reflectionClass->getTraitNames(), true)) {
return false;
}
}

return true;
},
"not to use traits '".implode("', '", $traits)."'",
FileLineFinder::where(fn (string $line): bool => str_contains($line, 'class')),
);
}






public function toImplement(array|string $interfaces): ArchExpectation
{
$interfaces = is_array($interfaces) ? $interfaces : [$interfaces];


$original = $this->original;

return Targeted::make(
$original,
function (ObjectDescription $object) use ($interfaces): bool {
foreach ($interfaces as $interface) {
if (isset($object->reflectionClass) && $object->reflectionClass->implementsInterface($interface)) {
return false;
}
}

return true;
},
"not to implement '".implode("', '", $interfaces)."'",
FileLineFinder::where(fn (string $line): bool => str_contains($line, 'class')),
);
}




public function toImplementNothing(): ArchExpectation
{

$original = $this->original;

return Targeted::make(
$original,
fn (ObjectDescription $object): bool => isset($object->reflectionClass) === false || $object->reflectionClass->getInterfaceNames() !== [],
'to implement an interface',
FileLineFinder::where(fn (string $line): bool => str_contains($line, 'class')),
);
}




public function toOnlyImplement(): void
{
throw InvalidExpectation::fromMethods(['not', 'toOnlyImplement']);
}




public function toHavePrefix(string $prefix): ArchExpectation
{

$original = $this->original;

return Targeted::make(
$original,
fn (ObjectDescription $object): bool => isset($object->reflectionClass) === false || ! str_starts_with($object->reflectionClass->getShortName(), $prefix),
"not to have prefix '{$prefix}'",
FileLineFinder::where(fn (string $line): bool => str_contains($line, 'class')),
);
}




public function toHaveSuffix(string $suffix): ArchExpectation
{

$original = $this->original;

return Targeted::make(
$original,
fn (ObjectDescription $object): bool => isset($object->reflectionClass) === false || ! str_ends_with($object->reflectionClass->getName(), $suffix),
"not to have suffix '{$suffix}'",
FileLineFinder::where(fn (string $line): bool => str_contains($line, 'class')),
);
}




public function toOnlyUse(): void
{
throw InvalidExpectation::fromMethods(['not', 'toOnlyUse']);
}




public function toUseNothing(): void
{
throw InvalidExpectation::fromMethods(['not', 'toUseNothing']);
}




public function toBeUsed(): ArchExpectation
{

$original = $this->original;

return ToBeUsedInNothing::make($original);
}






public function toBeUsedIn(array|string $targets): ArchExpectation
{

$original = $this->original;

return GroupArchExpectation::fromExpectations($original, array_map(fn (string $target): GroupArchExpectation => ToBeUsedIn::make($original, $target)->opposite(
fn () => $this->throwExpectationFailedException('toBeUsedIn', $target),
), is_string($targets) ? [$targets] : $targets));
}

public function toOnlyBeUsedIn(): void
{
throw InvalidExpectation::fromMethods(['not', 'toOnlyBeUsedIn']);
}




public function toBeUsedInNothing(): void
{
throw InvalidExpectation::fromMethods(['not', 'toBeUsedInNothing']);
}




public function toBeInvokable(): ArchExpectation
{

$original = $this->original;

return Targeted::make(
$original,
fn (ObjectDescription $object): bool => isset($object->reflectionClass) === false || ! $object->reflectionClass->hasMethod('__invoke'),
'to not be invokable',
FileLineFinder::where(fn (string $line): bool => str_contains($line, 'class'))
);
}




public function toHaveAttribute(string $attribute): ArchExpectation
{

$original = $this->original;

return Targeted::make(
$original,
fn (ObjectDescription $object): bool => isset($object->reflectionClass) === false || $object->reflectionClass->getAttributes($attribute) === [],
"to not have attribute '{$attribute}'",
FileLineFinder::where(fn (string $line): bool => str_contains($line, 'class'))
);
}







public function __call(string $name, array $arguments): Expectation
{
try {
if (! is_object($this->original->value) && method_exists(PendingArchExpectation::class, $name)) {
throw InvalidExpectation::fromMethods(['not', $name]);
}


$this->original->{$name}(...$arguments);
} catch (ExpectationFailedException|AssertionFailedError) {
return $this->original;
}

$this->throwExpectationFailedException($name, $arguments);
}






public function __get(string $name): Expectation
{
try {
if (! is_object($this->original->value) && method_exists(PendingArchExpectation::class, $name)) {
throw InvalidExpectation::fromMethods(['not', $name]);
}

$this->original->{$name}; 
} catch (ExpectationFailedException) {
return $this->original;
}

$this->throwExpectationFailedException($name);
}






public function throwExpectationFailedException(string $name, array|string $arguments = []): never
{
$arguments = is_array($arguments) ? $arguments : [$arguments];

$exporter = Exporter::default();

$toString = fn (mixed $argument): string => $exporter->shortenedExport($argument);

throw new ExpectationFailedException(sprintf(
'Expecting %s not %s %s.',
$toString($this->original->value),
strtolower((string) preg_replace('/(?<!\ )[A-Z]/', ' $0', $name)),
implode(' ', array_map(fn (mixed $argument): string => $toString($argument), $arguments)),
));
}




public function toHaveConstructor(): ArchExpectation
{
return $this->toHaveMethod('__construct');
}




public function toHaveDestructor(): ArchExpectation
{
return $this->toHaveMethod('__destruct');
}




private function toBeBackedEnum(string $backingType): ArchExpectation
{

$original = $this->original;

return Targeted::make(
$original,
fn (ObjectDescription $object): bool => isset($object->reflectionClass) === false
|| ! $object->reflectionClass->isEnum()
|| ! (new \ReflectionEnum($object->name))->isBacked() 
|| (string) (new \ReflectionEnum($object->name))->getBackingType() !== $backingType, 
'not to be '.$backingType.' backed enum',
FileLineFinder::where(fn (string $line): bool => str_contains($line, 'class')),
);
}




public function toBeStringBackedEnums(): ArchExpectation
{
return $this->toBeStringBackedEnum();
}




public function toBeIntBackedEnums(): ArchExpectation
{
return $this->toBeIntBackedEnum();
}




public function toBeStringBackedEnum(): ArchExpectation
{
return $this->toBeBackedEnum('string');
}




public function toBeIntBackedEnum(): ArchExpectation
{
return $this->toBeBackedEnum('int');
}
}
