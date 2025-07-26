<?php declare(strict_types=1);

namespace PhpParser\Node\Stmt;

use PhpParser\Node;

class While_ extends Node\Stmt {

public Node\Expr $cond;

public array $stmts;








public function __construct(Node\Expr $cond, array $stmts = [], array $attributes = []) {
$this->attributes = $attributes;
$this->cond = $cond;
$this->stmts = $stmts;
}

public function getSubNodeNames(): array {
return ['cond', 'stmts'];
}

public function getType(): string {
return 'Stmt_While';
}
}
