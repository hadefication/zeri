<?php declare(strict_types=1);

namespace PhpParser\Node\Stmt;

use PhpParser\Modifiers;
use PhpParser\Node;

class ClassConst extends Node\Stmt {

public int $flags;

public array $consts;

public array $attrGroups;

public ?Node $type;










public function __construct(
array $consts,
int $flags = 0,
array $attributes = [],
array $attrGroups = [],
?Node $type = null
) {
$this->attributes = $attributes;
$this->flags = $flags;
$this->consts = $consts;
$this->attrGroups = $attrGroups;
$this->type = $type;
}

public function getSubNodeNames(): array {
return ['attrGroups', 'flags', 'type', 'consts'];
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




public function isFinal(): bool {
return (bool) ($this->flags & Modifiers::FINAL);
}

public function getType(): string {
return 'Stmt_ClassConst';
}
}
