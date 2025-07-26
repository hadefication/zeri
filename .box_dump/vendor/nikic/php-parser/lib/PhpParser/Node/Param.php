<?php declare(strict_types=1);

namespace PhpParser\Node;

use PhpParser\Modifiers;
use PhpParser\Node;
use PhpParser\NodeAbstract;

class Param extends NodeAbstract {

public ?Node $type;

public bool $byRef;

public bool $variadic;

public Expr $var;

public ?Expr $default;

public int $flags;

public array $attrGroups;

public array $hooks;














public function __construct(
Expr $var, ?Expr $default = null, ?Node $type = null,
bool $byRef = false, bool $variadic = false,
array $attributes = [],
int $flags = 0,
array $attrGroups = [],
array $hooks = []
) {
$this->attributes = $attributes;
$this->type = $type;
$this->byRef = $byRef;
$this->variadic = $variadic;
$this->var = $var;
$this->default = $default;
$this->flags = $flags;
$this->attrGroups = $attrGroups;
$this->hooks = $hooks;
}

public function getSubNodeNames(): array {
return ['attrGroups', 'flags', 'type', 'byRef', 'variadic', 'var', 'default', 'hooks'];
}

public function getType(): string {
return 'Param';
}




public function isPromoted(): bool {
return $this->flags !== 0 || $this->hooks !== [];
}

public function isPublic(): bool {
$public = (bool) ($this->flags & Modifiers::PUBLIC);
if ($public) {
return true;
}

if ($this->hooks === []) {
return false;
}

return ($this->flags & Modifiers::VISIBILITY_MASK) === 0;
}

public function isProtected(): bool {
return (bool) ($this->flags & Modifiers::PROTECTED);
}

public function isPrivate(): bool {
return (bool) ($this->flags & Modifiers::PRIVATE);
}

public function isReadonly(): bool {
return (bool) ($this->flags & Modifiers::READONLY);
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
}
