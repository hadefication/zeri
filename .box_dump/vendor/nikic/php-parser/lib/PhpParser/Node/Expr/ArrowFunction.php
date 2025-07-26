<?php declare(strict_types=1);

namespace PhpParser\Node\Expr;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\FunctionLike;

class ArrowFunction extends Expr implements FunctionLike {

public bool $static;


public bool $byRef;


public array $params = [];


public ?Node $returnType;


public Expr $expr;

public array $attrGroups;


















public function __construct(array $subNodes, array $attributes = []) {
$this->attributes = $attributes;
$this->static = $subNodes['static'] ?? false;
$this->byRef = $subNodes['byRef'] ?? false;
$this->params = $subNodes['params'] ?? [];
$this->returnType = $subNodes['returnType'] ?? null;
$this->expr = $subNodes['expr'];
$this->attrGroups = $subNodes['attrGroups'] ?? [];
}

public function getSubNodeNames(): array {
return ['attrGroups', 'static', 'byRef', 'params', 'returnType', 'expr'];
}

public function returnsByRef(): bool {
return $this->byRef;
}

public function getParams(): array {
return $this->params;
}

public function getReturnType() {
return $this->returnType;
}

public function getAttrGroups(): array {
return $this->attrGroups;
}




public function getStmts(): array {
return [new Node\Stmt\Return_($this->expr)];
}

public function getType(): string {
return 'Expr_ArrowFunction';
}
}
