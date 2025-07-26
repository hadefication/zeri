<?php declare(strict_types=1);

namespace PhpParser\Node\Stmt;

use PhpParser\Node;

class If_ extends Node\Stmt {

public Node\Expr $cond;

public array $stmts;

public array $elseifs;

public ?Else_ $else;















public function __construct(Node\Expr $cond, array $subNodes = [], array $attributes = []) {
$this->attributes = $attributes;
$this->cond = $cond;
$this->stmts = $subNodes['stmts'] ?? [];
$this->elseifs = $subNodes['elseifs'] ?? [];
$this->else = $subNodes['else'] ?? null;
}

public function getSubNodeNames(): array {
return ['cond', 'stmts', 'elseifs', 'else'];
}

public function getType(): string {
return 'Stmt_If';
}
}
