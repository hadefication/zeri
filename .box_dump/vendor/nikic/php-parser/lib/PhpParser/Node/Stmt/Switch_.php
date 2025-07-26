<?php declare(strict_types=1);

namespace PhpParser\Node\Stmt;

use PhpParser\Node;

class Switch_ extends Node\Stmt {

public Node\Expr $cond;

public array $cases;








public function __construct(Node\Expr $cond, array $cases, array $attributes = []) {
$this->attributes = $attributes;
$this->cond = $cond;
$this->cases = $cases;
}

public function getSubNodeNames(): array {
return ['cond', 'cases'];
}

public function getType(): string {
return 'Stmt_Switch';
}
}
