<?php declare(strict_types=1);

namespace PhpParser\Node\Stmt;

use PhpParser\Modifiers;
use PhpParser\Node;

class Class_ extends ClassLike {

public const MODIFIER_PUBLIC = 1;

public const MODIFIER_PROTECTED = 2;

public const MODIFIER_PRIVATE = 4;

public const MODIFIER_STATIC = 8;

public const MODIFIER_ABSTRACT = 16;

public const MODIFIER_FINAL = 32;

public const MODIFIER_READONLY = 64;


public const VISIBILITY_MODIFIER_MASK = 7; 


public int $flags;

public ?Node\Name $extends;

public array $implements;



















public function __construct($name, array $subNodes = [], array $attributes = []) {
$this->attributes = $attributes;
$this->flags = $subNodes['flags'] ?? $subNodes['type'] ?? 0;
$this->name = \is_string($name) ? new Node\Identifier($name) : $name;
$this->extends = $subNodes['extends'] ?? null;
$this->implements = $subNodes['implements'] ?? [];
$this->stmts = $subNodes['stmts'] ?? [];
$this->attrGroups = $subNodes['attrGroups'] ?? [];
}

public function getSubNodeNames(): array {
return ['attrGroups', 'flags', 'name', 'extends', 'implements', 'stmts'];
}




public function isAbstract(): bool {
return (bool) ($this->flags & Modifiers::ABSTRACT);
}




public function isFinal(): bool {
return (bool) ($this->flags & Modifiers::FINAL);
}

public function isReadonly(): bool {
return (bool) ($this->flags & Modifiers::READONLY);
}




public function isAnonymous(): bool {
return null === $this->name;
}

public function getType(): string {
return 'Stmt_Class';
}
}
