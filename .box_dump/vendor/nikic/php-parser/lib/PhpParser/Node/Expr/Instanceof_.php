<?php declare(strict_types=1);

namespace PhpParser\Node\Expr;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Name;

class Instanceof_ extends Expr {

public Expr $expr;

public Node $class;








public function __construct(Expr $expr, Node $class, array $attributes = []) {
$this->attributes = $attributes;
$this->expr = $expr;
$this->class = $class;
}

public function getSubNodeNames(): array {
return ['expr', 'class'];
}

public function getType(): string {
return 'Expr_Instanceof';
}
}
