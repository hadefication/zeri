<?php declare(strict_types=1);

namespace PhpParser\Node;

class IntersectionType extends ComplexType {

public array $types;







public function __construct(array $types, array $attributes = []) {
$this->attributes = $attributes;
$this->types = $types;
}

public function getSubNodeNames(): array {
return ['types'];
}

public function getType(): string {
return 'IntersectionType';
}
}
