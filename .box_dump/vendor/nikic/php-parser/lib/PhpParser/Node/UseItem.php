<?php declare(strict_types=1);

namespace PhpParser\Node;

use PhpParser\Node;
use PhpParser\NodeAbstract;
use PhpParser\Node\Stmt\Use_;

class UseItem extends NodeAbstract {



public int $type;

public Name $name;

public ?Identifier $alias;









public function __construct(Node\Name $name, $alias = null, int $type = Use_::TYPE_UNKNOWN, array $attributes = []) {
$this->attributes = $attributes;
$this->type = $type;
$this->name = $name;
$this->alias = \is_string($alias) ? new Identifier($alias) : $alias;
}

public function getSubNodeNames(): array {
return ['type', 'name', 'alias'];
}




public function getAlias(): Identifier {
if (null !== $this->alias) {
return $this->alias;
}

return new Identifier($this->name->getLast());
}

public function getType(): string {
return 'UseItem';
}
}


class_alias(UseItem::class, Stmt\UseUse::class);
