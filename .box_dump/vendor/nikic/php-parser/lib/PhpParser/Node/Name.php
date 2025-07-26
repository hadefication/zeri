<?php declare(strict_types=1);

namespace PhpParser\Node;

use PhpParser\NodeAbstract;

class Name extends NodeAbstract {
/**
@psalm-var

*/
public string $name;


private static array $specialClassNames = [
'self' => true,
'parent' => true,
'static' => true,
];







final public function __construct($name, array $attributes = []) {
$this->attributes = $attributes;
$this->name = self::prepareName($name);
}

public function getSubNodeNames(): array {
return ['name'];
}

/**
@psalm-return



*/
public function getParts(): array {
return \explode('\\', $this->name);
}






public function getFirst(): string {
if (false !== $pos = \strpos($this->name, '\\')) {
return \substr($this->name, 0, $pos);
}
return $this->name;
}






public function getLast(): string {
if (false !== $pos = \strrpos($this->name, '\\')) {
return \substr($this->name, $pos + 1);
}
return $this->name;
}






public function isUnqualified(): bool {
return false === \strpos($this->name, '\\');
}






public function isQualified(): bool {
return false !== \strpos($this->name, '\\');
}






public function isFullyQualified(): bool {
return false;
}






public function isRelative(): bool {
return false;
}

/**
@psalm-return




*/
public function toString(): string {
return $this->name;
}

/**
@psalm-return




*/
public function toCodeString(): string {
return $this->toString();
}

/**
@psalm-return




*/
public function toLowerString(): string {
return strtolower($this->name);
}






public function isSpecialClassName(): bool {
return isset(self::$specialClassNames[strtolower($this->name)]);
}

/**
@psalm-return




*/
public function __toString(): string {
return $this->name;
}

















public function slice(int $offset, ?int $length = null) {
if ($offset === 1 && $length === null) {

if (false !== $pos = \strpos($this->name, '\\')) {
return new static(\substr($this->name, $pos + 1));
}
return null;
}

$parts = \explode('\\', $this->name);
$numParts = \count($parts);

$realOffset = $offset < 0 ? $offset + $numParts : $offset;
if ($realOffset < 0 || $realOffset > $numParts) {
throw new \OutOfBoundsException(sprintf('Offset %d is out of bounds', $offset));
}

if (null === $length) {
$realLength = $numParts - $realOffset;
} else {
$realLength = $length < 0 ? $length + $numParts - $realOffset : $length;
if ($realLength < 0 || $realLength > $numParts - $realOffset) {
throw new \OutOfBoundsException(sprintf('Length %d is out of bounds', $length));
}
}

if ($realLength === 0) {

return null;
}

return new static(array_slice($parts, $realOffset, $realLength), $this->attributes);
}


















public static function concat($name1, $name2, array $attributes = []) {
if (null === $name1 && null === $name2) {
return null;
}
if (null === $name1) {
return new static($name2, $attributes);
}
if (null === $name2) {
return new static($name1, $attributes);
} else {
return new static(
self::prepareName($name1) . '\\' . self::prepareName($name2), $attributes
);
}
}

/**
@psalm-return






*/
private static function prepareName($name): string {
if (\is_string($name)) {
if ('' === $name) {
throw new \InvalidArgumentException('Name cannot be empty');
}

return $name;
}
if (\is_array($name)) {
if (empty($name)) {
throw new \InvalidArgumentException('Name cannot be empty');
}

return implode('\\', $name);
}
if ($name instanceof self) {
return $name->name;
}

throw new \InvalidArgumentException(
'Expected string, array of parts or Name instance'
);
}

public function getType(): string {
return 'Name';
}
}
