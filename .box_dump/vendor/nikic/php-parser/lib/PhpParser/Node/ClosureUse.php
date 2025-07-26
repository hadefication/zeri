<?php declare(strict_types=1);

namespace PhpParser\Node;

use PhpParser\NodeAbstract;

class ClosureUse extends NodeAbstract {

public Expr\Variable $var;

public bool $byRef;








public function __construct(Expr\Variable $var, bool $byRef = false, array $attributes = []) {
$this->attributes = $attributes;
$this->var = $var;
$this->byRef = $byRef;
}

public function getSubNodeNames(): array {
return ['var', 'byRef'];
}

public function getType(): string {
return 'ClosureUse';
}
}


class_alias(ClosureUse::class, Expr\ClosureUse::class);
