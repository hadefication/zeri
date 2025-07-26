<?php declare(strict_types=1);

namespace PhpParser\Node\Expr;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Name;
use PhpParser\Node\VarLikeIdentifier;

class StaticPropertyFetch extends Expr {

public Node $class;

public Node $name;








public function __construct(Node $class, $name, array $attributes = []) {
$this->attributes = $attributes;
$this->class = $class;
$this->name = \is_string($name) ? new VarLikeIdentifier($name) : $name;
}

public function getSubNodeNames(): array {
return ['class', 'name'];
}

public function getType(): string {
return 'Expr_StaticPropertyFetch';
}
}
