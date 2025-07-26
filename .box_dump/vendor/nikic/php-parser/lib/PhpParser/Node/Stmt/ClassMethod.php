<?php declare(strict_types=1);

namespace PhpParser\Node\Stmt;

use PhpParser\Modifiers;
use PhpParser\Node;
use PhpParser\Node\FunctionLike;

class ClassMethod extends Node\Stmt implements FunctionLike {

public int $flags;

public bool $byRef;

public Node\Identifier $name;

public array $params;

public ?Node $returnType;

public ?array $stmts;

public array $attrGroups;


private static array $magicNames = [
'__construct' => true,
'__destruct' => true,
'__call' => true,
'__callstatic' => true,
'__get' => true,
'__set' => true,
'__isset' => true,
'__unset' => true,
'__sleep' => true,
'__wakeup' => true,
'__tostring' => true,
'__set_state' => true,
'__clone' => true,
'__invoke' => true,
'__debuginfo' => true,
'__serialize' => true,
'__unserialize' => true,
];





















public function __construct($name, array $subNodes = [], array $attributes = []) {
$this->attributes = $attributes;
$this->flags = $subNodes['flags'] ?? $subNodes['type'] ?? 0;
$this->byRef = $subNodes['byRef'] ?? false;
$this->name = \is_string($name) ? new Node\Identifier($name) : $name;
$this->params = $subNodes['params'] ?? [];
$this->returnType = $subNodes['returnType'] ?? null;
$this->stmts = array_key_exists('stmts', $subNodes) ? $subNodes['stmts'] : [];
$this->attrGroups = $subNodes['attrGroups'] ?? [];
}

public function getSubNodeNames(): array {
return ['attrGroups', 'flags', 'byRef', 'name', 'params', 'returnType', 'stmts'];
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

public function getStmts(): ?array {
return $this->stmts;
}

public function getAttrGroups(): array {
return $this->attrGroups;
}




public function isPublic(): bool {
return ($this->flags & Modifiers::PUBLIC) !== 0
|| ($this->flags & Modifiers::VISIBILITY_MASK) === 0;
}




public function isProtected(): bool {
return (bool) ($this->flags & Modifiers::PROTECTED);
}




public function isPrivate(): bool {
return (bool) ($this->flags & Modifiers::PRIVATE);
}




public function isAbstract(): bool {
return (bool) ($this->flags & Modifiers::ABSTRACT);
}




public function isFinal(): bool {
return (bool) ($this->flags & Modifiers::FINAL);
}




public function isStatic(): bool {
return (bool) ($this->flags & Modifiers::STATIC);
}




public function isMagic(): bool {
return isset(self::$magicNames[$this->name->toLowerString()]);
}

public function getType(): string {
return 'Stmt_ClassMethod';
}
}
