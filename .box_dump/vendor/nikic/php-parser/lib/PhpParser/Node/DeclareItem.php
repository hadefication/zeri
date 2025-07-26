<?php declare(strict_types=1);

namespace PhpParser\Node;

use PhpParser\Node;
use PhpParser\NodeAbstract;

class DeclareItem extends NodeAbstract {

public Identifier $key;

public Expr $value;








public function __construct($key, Node\Expr $value, array $attributes = []) {
$this->attributes = $attributes;
$this->key = \is_string($key) ? new Node\Identifier($key) : $key;
$this->value = $value;
}

public function getSubNodeNames(): array {
return ['key', 'value'];
}

public function getType(): string {
return 'DeclareItem';
}
}


class_alias(DeclareItem::class, Stmt\DeclareDeclare::class);
