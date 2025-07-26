<?php declare(strict_types=1);

namespace PhpParser\Node\Stmt;

use PhpParser\Node;

class Enum_ extends ClassLike {

public ?Node $scalarType;

public array $implements;















public function __construct($name, array $subNodes = [], array $attributes = []) {
$this->name = \is_string($name) ? new Node\Identifier($name) : $name;
$this->scalarType = $subNodes['scalarType'] ?? null;
$this->implements = $subNodes['implements'] ?? [];
$this->stmts = $subNodes['stmts'] ?? [];
$this->attrGroups = $subNodes['attrGroups'] ?? [];

parent::__construct($attributes);
}

public function getSubNodeNames(): array {
return ['attrGroups', 'name', 'scalarType', 'implements', 'stmts'];
}

public function getType(): string {
return 'Stmt_Enum';
}
}
