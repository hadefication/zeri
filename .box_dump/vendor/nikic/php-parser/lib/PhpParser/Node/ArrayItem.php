<?php declare(strict_types=1);

namespace PhpParser\Node;

use PhpParser\NodeAbstract;

class ArrayItem extends NodeAbstract {

public ?Expr $key;

public Expr $value;

public bool $byRef;

public bool $unpack;









public function __construct(Expr $value, ?Expr $key = null, bool $byRef = false, array $attributes = [], bool $unpack = false) {
$this->attributes = $attributes;
$this->key = $key;
$this->value = $value;
$this->byRef = $byRef;
$this->unpack = $unpack;
}

public function getSubNodeNames(): array {
return ['key', 'value', 'byRef', 'unpack'];
}

public function getType(): string {
return 'ArrayItem';
}
}


class_alias(ArrayItem::class, Expr\ArrayItem::class);
