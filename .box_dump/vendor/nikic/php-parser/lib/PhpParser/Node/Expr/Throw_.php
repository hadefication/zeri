<?php declare(strict_types=1);

namespace PhpParser\Node\Expr;

use PhpParser\Node;

class Throw_ extends Node\Expr {

public Node\Expr $expr;







public function __construct(Node\Expr $expr, array $attributes = []) {
$this->attributes = $attributes;
$this->expr = $expr;
}

public function getSubNodeNames(): array {
return ['expr'];
}

public function getType(): string {
return 'Expr_Throw';
}
}
