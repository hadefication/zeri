<?php declare(strict_types=1);

namespace PhpParser\Node;

use PhpParser\NodeAbstract;




class Identifier extends NodeAbstract {
/**
@psalm-var

*/
public string $name;


private static array $specialClassNames = [
'self' => true,
'parent' => true,
'static' => true,
];







public function __construct(string $name, array $attributes = []) {
if ($name === '') {
throw new \InvalidArgumentException('Identifier name cannot be empty');
}

$this->attributes = $attributes;
$this->name = $name;
}

public function getSubNodeNames(): array {
return ['name'];
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

public function getType(): string {
return 'Identifier';
}
}
