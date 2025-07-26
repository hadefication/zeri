<?php









namespace Mockery;

use InvalidArgumentException;
use ReflectionClass;
use ReflectionIntersectionType;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionType;
use ReflectionUnionType;

use function array_diff;
use function array_intersect;
use function array_map;
use function array_merge;
use function get_debug_type;
use function implode;
use function in_array;
use function method_exists;
use function sprintf;
use function strpos;

use const PHP_VERSION_ID;




class Reflector
{





public const BUILTIN_TYPES = ['array', 'bool', 'int', 'float', 'null', 'object', 'string'];






public const RESERVED_WORDS = ['bool', 'true', 'false', 'float', 'int', 'iterable', 'mixed', 'never', 'null', 'object', 'string', 'void'];






private const ITERABLE = ['iterable'];






private const TRAVERSABLE_ARRAY = ['\Traversable', 'array'];








public static function getReturnType(ReflectionMethod $method, $withoutNullable = false)
{
$type = $method->getReturnType();

if (! $type instanceof ReflectionType && method_exists($method, 'getTentativeReturnType')) {
$type = $method->getTentativeReturnType();
}

if (! $type instanceof ReflectionType) {
return null;
}

$typeHint = self::getTypeFromReflectionType($type, $method->getDeclaringClass());

return (! $withoutNullable && $type->allowsNull()) ? self::formatNullableType($typeHint) : $typeHint;
}






public static function getSimplestReturnType(ReflectionMethod $method)
{
$type = $method->getReturnType();

if (! $type instanceof ReflectionType && method_exists($method, 'getTentativeReturnType')) {
$type = $method->getTentativeReturnType();
}

if (! $type instanceof ReflectionType || $type->allowsNull()) {
return null;
}

$typeInformation = self::getTypeInformation($type, $method->getDeclaringClass());


foreach ($typeInformation as $info) {
if ($info['isPrimitive']) {
return $info['typeHint'];
}
}


foreach ($typeInformation as $info) {
return $info['typeHint'];
}

return null;
}








public static function getTypeHint(ReflectionParameter $param, $withoutNullable = false)
{
if (! $param->hasType()) {
return null;
}

$type = $param->getType();
$declaringClass = $param->getDeclaringClass();
$typeHint = self::getTypeFromReflectionType($type, $declaringClass);

return (! $withoutNullable && $type->allowsNull()) ? self::formatNullableType($typeHint) : $typeHint;
}






public static function isArray(ReflectionParameter $param)
{
$type = $param->getType();

return $type instanceof ReflectionNamedType && $type->getName();
}




public static function isReservedWord(string $type): bool
{
return in_array(strtolower($type), self::RESERVED_WORDS, true);
}




private static function formatNullableType(string $typeHint): string
{
if ($typeHint === 'mixed') {
return $typeHint;
}

if (strpos($typeHint, 'null') !== false) {
return $typeHint;
}

if (PHP_VERSION_ID < 80000) {
return sprintf('?%s', $typeHint);
}

return sprintf('%s|null', $typeHint);
}

private static function getTypeFromReflectionType(ReflectionType $type, ReflectionClass $declaringClass): string
{
if ($type instanceof ReflectionNamedType) {
$typeHint = $type->getName();

if ($type->isBuiltin()) {
return $typeHint;
}

if ($typeHint === 'static') {
return $typeHint;
}


if ($typeHint === 'self') {
$typeHint = $declaringClass->getName();
}


if ($typeHint === 'parent') {
$typeHint = $declaringClass->getParentClass()->getName();
}


return sprintf('\\%s', $typeHint);
}

if ($type instanceof ReflectionIntersectionType) {
$types = array_map(
static function (ReflectionType $type) use ($declaringClass): string {
return self::getTypeFromReflectionType($type, $declaringClass);
},
$type->getTypes()
);

return implode('&', $types);
}

if ($type instanceof ReflectionUnionType) {
$types = array_map(
static function (ReflectionType $type) use ($declaringClass): string {
return self::getTypeFromReflectionType($type, $declaringClass);
},
$type->getTypes()
);

$intersect = array_intersect(self::TRAVERSABLE_ARRAY, $types);
if ($intersect === self::TRAVERSABLE_ARRAY) {
$types = array_merge(self::ITERABLE, array_diff($types, self::TRAVERSABLE_ARRAY));
}

return implode(
'|',
array_map(
static function (string $type): string {
return strpos($type, '&') === false ? $type : sprintf('(%s)', $type);
},
$types
)
);
}

throw new InvalidArgumentException('Unknown ReflectionType: ' . get_debug_type($type));
}






private static function getTypeInformation(ReflectionType $type, ReflectionClass $declaringClass): array
{

if ($type instanceof ReflectionUnionType || $type instanceof ReflectionIntersectionType) {
$types = [];

foreach ($type->getTypes() as $innterType) {
foreach (self::getTypeInformation($innterType, $declaringClass) as $info) {
if ($info['typeHint'] === 'null' && $info['isPrimitive']) {
continue;
}

$types[] = $info;
}
}

return $types;
}


$typeHint = $type->getName();


if ($type->isBuiltin()) {
return [
[
'typeHint' => $typeHint,
'isPrimitive' => in_array($typeHint, self::BUILTIN_TYPES, true),
],
];
}


if ($typeHint === 'static') {
return [
[
'typeHint' => $typeHint,
'isPrimitive' => false,
],
];
}


if ($typeHint === 'self') {
$typeHint = $declaringClass->getName();
}


if ($typeHint === 'parent') {
$typeHint = $declaringClass->getParentClass()->getName();
}


return [
[
'typeHint' => sprintf('\\%s', $typeHint),
'isPrimitive' => false,
],
];
}
}
