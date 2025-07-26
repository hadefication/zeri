<?php declare(strict_types=1);

namespace PhpParser\Node\Stmt;

use PhpParser\Modifiers;
use PhpParser\Node;
use PhpParser\Node\ComplexType;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\PropertyItem;

class Property extends Node\Stmt {

public int $flags;

public array $props;

public ?Node $type;

public array $attrGroups;

public array $hooks;











public function __construct(int $flags, array $props, array $attributes = [], ?Node $type = null, array $attrGroups = [], array $hooks = []) {
$this->attributes = $attributes;
$this->flags = $flags;
$this->props = $props;
$this->type = $type;
$this->attrGroups = $attrGroups;
$this->hooks = $hooks;
}

public function getSubNodeNames(): array {
return ['attrGroups', 'flags', 'type', 'props', 'hooks'];
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




public function isStatic(): bool {
return (bool) ($this->flags & Modifiers::STATIC);
}




public function isReadonly(): bool {
return (bool) ($this->flags & Modifiers::READONLY);
}




public function isAbstract(): bool {
return (bool) ($this->flags & Modifiers::ABSTRACT);
}




public function isFinal(): bool {
return (bool) ($this->flags & Modifiers::FINAL);
}




public function isPublicSet(): bool {
return (bool) ($this->flags & Modifiers::PUBLIC_SET);
}




public function isProtectedSet(): bool {
return (bool) ($this->flags & Modifiers::PROTECTED_SET);
}




public function isPrivateSet(): bool {
return (bool) ($this->flags & Modifiers::PRIVATE_SET);
}

public function getType(): string {
return 'Stmt_Property';
}
}
