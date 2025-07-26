<?php declare(strict_types=1);

namespace PhpParser\Node\Expr;

use PhpParser\Node;
use PhpParser\Node\Expr;

class FuncCall extends CallLike {

public Node $name;

public array $args;








public function __construct(Node $name, array $args = [], array $attributes = []) {
$this->attributes = $attributes;
$this->name = $name;
$this->args = $args;
}

public function getSubNodeNames(): array {
return ['name', 'args'];
}

public function getType(): string {
return 'Expr_FuncCall';
}

public function getRawArgs(): array {
return $this->args;
}
}
