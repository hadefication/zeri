<?php declare(strict_types=1);

namespace PhpParser\Node;

use PhpParser\Modifiers;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\Return_;
use PhpParser\NodeAbstract;

class PropertyHook extends NodeAbstract implements FunctionLike {

public array $attrGroups;

public int $flags;

public bool $byRef;

public Identifier $name;

public array $params;

public $body;


















public function __construct($name, $body, array $subNodes = [], array $attributes = []) {
$this->attributes = $attributes;
$this->name = \is_string($name) ? new Identifier($name) : $name;
$this->body = $body;
$this->flags = $subNodes['flags'] ?? 0;
$this->byRef = $subNodes['byRef'] ?? false;
$this->params = $subNodes['params'] ?? [];
$this->attrGroups = $subNodes['attrGroups'] ?? [];
}

public function returnsByRef(): bool {
return $this->byRef;
}

public function getParams(): array {
return $this->params;
}

public function getReturnType() {
return null;
}




public function isFinal(): bool {
return (bool) ($this->flags & Modifiers::FINAL);
}

public function getStmts(): ?array {
if ($this->body instanceof Expr) {
$name = $this->name->toLowerString();
if ($name === 'get') {
return [new Return_($this->body)];
}
if ($name === 'set') {
if (!$this->hasAttribute('propertyName')) {
throw new \LogicException(
'Can only use getStmts() on a "set" hook if the "propertyName" attribute is set');
}

$propName = $this->getAttribute('propertyName');
$prop = new PropertyFetch(new Variable('this'), (string) $propName);
return [new Expression(new Assign($prop, $this->body))];
}
throw new \LogicException('Unknown property hook "' . $name . '"');
}
return $this->body;
}

public function getAttrGroups(): array {
return $this->attrGroups;
}

public function getType(): string {
return 'PropertyHook';
}

public function getSubNodeNames(): array {
return ['attrGroups', 'flags', 'byRef', 'name', 'params', 'body'];
}
}
