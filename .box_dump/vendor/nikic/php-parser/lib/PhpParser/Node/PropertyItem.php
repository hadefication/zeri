<?php declare(strict_types=1);

namespace PhpParser\Node;

use PhpParser\Node;
use PhpParser\NodeAbstract;

class PropertyItem extends NodeAbstract {

public VarLikeIdentifier $name;

public ?Expr $default;








public function __construct($name, ?Node\Expr $default = null, array $attributes = []) {
$this->attributes = $attributes;
$this->name = \is_string($name) ? new Node\VarLikeIdentifier($name) : $name;
$this->default = $default;
}

public function getSubNodeNames(): array {
return ['name', 'default'];
}

public function getType(): string {
return 'PropertyItem';
}
}


class_alias(PropertyItem::class, Stmt\PropertyProperty::class);
