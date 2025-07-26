<?php declare(strict_types=1);








namespace SebastianBergmann\Type;

use function assert;
use function count;
use function implode;
use function sort;

/**
@no-named-arguments
*/
final class UnionType extends Type
{



private array $types;




public function __construct(Type ...$types)
{
$this->ensureMinimumOfTwoTypes(...$types);
$this->ensureOnlyValidTypes(...$types);

assert(!empty($types));

$this->types = $types;
}

public function isAssignable(Type $other): bool
{
foreach ($this->types as $type) {
if ($type->isAssignable($other)) {
return true;
}
}

return false;
}




public function asString(): string
{
return $this->name();
}




public function name(): string
{
$types = [];

foreach ($this->types as $type) {
if ($type->isIntersection()) {
$types[] = '(' . $type->name() . ')';

continue;
}

$types[] = $type->name();
}

sort($types);

return implode('|', $types);
}

public function allowsNull(): bool
{
foreach ($this->types as $type) {
if ($type instanceof NullType) {
return true;
}
}

return false;
}

public function isUnion(): bool
{
return true;
}

public function containsIntersectionTypes(): bool
{
foreach ($this->types as $type) {
if ($type->isIntersection()) {
return true;
}
}

return false;
}




public function types(): array
{
return $this->types;
}




private function ensureMinimumOfTwoTypes(Type ...$types): void
{
if (count($types) < 2) {
throw new RuntimeException(
'A union type must be composed of at least two types',
);
}
}




private function ensureOnlyValidTypes(Type ...$types): void
{
foreach ($types as $type) {
if ($type instanceof UnknownType) {
throw new RuntimeException(
'A union type must not be composed of an unknown type',
);
}

if ($type instanceof VoidType) {
throw new RuntimeException(
'A union type must not be composed of a void type',
);
}
}
}
}
