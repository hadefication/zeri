<?php declare(strict_types=1);

namespace PhpParser\Node\Expr;

use PhpParser\Node;
use PhpParser\Node\ClosureUse;
use PhpParser\Node\Expr;
use PhpParser\Node\FunctionLike;

class Closure extends Expr implements FunctionLike {

public bool $static;

public bool $byRef;

public array $params;

public array $uses;

public ?Node $returnType;

public array $stmts;

public array $attrGroups;






















public function __construct(array $subNodes = [], array $attributes = []) {
$this->attributes = $attributes;
$this->static = $subNodes['static'] ?? false;
$this->byRef = $subNodes['byRef'] ?? false;
$this->params = $subNodes['params'] ?? [];
$this->uses = $subNodes['uses'] ?? [];
$this->returnType = $subNodes['returnType'] ?? null;
$this->stmts = $subNodes['stmts'] ?? [];
$this->attrGroups = $subNodes['attrGroups'] ?? [];
}

public function getSubNodeNames(): array {
return ['attrGroups', 'static', 'byRef', 'params', 'uses', 'returnType', 'stmts'];
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


public function getStmts(): array {
return $this->stmts;
}

public function getAttrGroups(): array {
return $this->attrGroups;
}

public function getType(): string {
return 'Expr_Closure';
}
}
