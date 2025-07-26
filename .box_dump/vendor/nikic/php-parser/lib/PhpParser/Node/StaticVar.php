<?php declare(strict_types=1);

namespace PhpParser\Node;

use PhpParser\Node;
use PhpParser\NodeAbstract;

class StaticVar extends NodeAbstract {

public Expr\Variable $var;

public ?Expr $default;








public function __construct(
Expr\Variable $var, ?Node\Expr $default = null, array $attributes = []
) {
$this->attributes = $attributes;
$this->var = $var;
$this->default = $default;
}

public function getSubNodeNames(): array {
return ['var', 'default'];
}

public function getType(): string {
return 'StaticVar';
}
}


class_alias(StaticVar::class, Stmt\StaticVar::class);
