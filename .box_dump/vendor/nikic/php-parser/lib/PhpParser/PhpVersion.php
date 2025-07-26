<?php declare(strict_types=1);

namespace PhpParser;




class PhpVersion {

public int $id;


private const BUILTIN_TYPE_VERSIONS = [
'array' => 50100,
'callable' => 50400,
'bool' => 70000,
'int' => 70000,
'float' => 70000,
'string' => 70000,
'iterable' => 70100,
'void' => 70100,
'object' => 70200,
'null' => 80000,
'false' => 80000,
'mixed' => 80000,
'never' => 80100,
'true' => 80200,
];

private function __construct(int $id) {
$this->id = $id;
}




public static function fromComponents(int $major, int $minor): self {
return new self($major * 10000 + $minor * 100);
}





public static function getNewestSupported(): self {
return self::fromComponents(8, 4);
}




public static function getHostVersion(): self {
return self::fromComponents(\PHP_MAJOR_VERSION, \PHP_MINOR_VERSION);
}




public static function fromString(string $version): self {
if (!preg_match('/^(\d+)\.(\d+)/', $version, $matches)) {
throw new \LogicException("Invalid PHP version \"$version\"");
}
return self::fromComponents((int) $matches[1], (int) $matches[2]);
}




public function equals(PhpVersion $other): bool {
return $this->id === $other->id;
}




public function newerOrEqual(PhpVersion $other): bool {
return $this->id >= $other->id;
}




public function older(PhpVersion $other): bool {
return $this->id < $other->id;
}




public function isHostVersion(): bool {
return $this->equals(self::getHostVersion());
}




public function supportsBuiltinType(string $type): bool {
$minVersion = self::BUILTIN_TYPE_VERSIONS[$type] ?? null;
return $minVersion !== null && $this->id >= $minVersion;
}




public function supportsShortArraySyntax(): bool {
return $this->id >= 50400;
}




public function supportsShortArrayDestructuring(): bool {
return $this->id >= 70100;
}




public function supportsFlexibleHeredoc(): bool {
return $this->id >= 70300;
}




public function supportsTrailingCommaInParamList(): bool {
return $this->id >= 80000;
}




public function allowsAssignNewByReference(): bool {
return $this->id < 70000;
}




public function allowsInvalidOctals(): bool {
return $this->id < 70000;
}




public function allowsDelInIdentifiers(): bool {
return $this->id < 70100;
}




public function supportsYieldWithoutParentheses(): bool {
return $this->id >= 70000;
}




public function supportsUnicodeEscapes(): bool {
return $this->id >= 70000;
}




public function supportsAttributes(): bool {
return $this->id >= 80000;
}
}
