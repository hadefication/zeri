<?php declare(strict_types=1);

namespace PhpParser\Node\Stmt;

use PhpParser\Node;
use PhpParser\Node\FunctionLike;

class Function_ extends Node\Stmt implements FunctionLike {

public bool $byRef;

public Node\Identifier $name;

public array $params;

public ?Node $returnType;

public array $stmts;

public array $attrGroups;


public ?Node\Name $namespacedName;



















public function __construct($name, array $subNodes = [], array $attributes = []) {
$this->attributes = $attributes;
$this->byRef = $subNodes['byRef'] ?? false;
$this->name = \is_string($name) ? new Node\Identifier($name) : $name;
$this->params = $subNodes['params'] ?? [];
$this->returnType = $subNodes['returnType'] ?? null;
$this->stmts = $subNodes['stmts'] ?? [];
$this->attrGroups = $subNodes['attrGroups'] ?? [];
}

public function getSubNodeNames(): array {
return ['attrGroups', 'byRef', 'name', 'params', 'returnType', 'stmts'];
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
return $this->stmts;
}

public function getType(): string {
return 'Stmt_Function';
}
}
