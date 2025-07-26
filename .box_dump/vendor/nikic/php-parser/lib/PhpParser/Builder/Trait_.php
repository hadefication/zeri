<?php declare(strict_types=1);

namespace PhpParser\Builder;

use PhpParser;
use PhpParser\BuilderHelpers;
use PhpParser\Node;
use PhpParser\Node\Stmt;

class Trait_ extends Declaration {
protected string $name;

protected array $uses = [];

protected array $constants = [];

protected array $properties = [];

protected array $methods = [];

protected array $attributeGroups = [];






public function __construct(string $name) {
$this->name = $name;
}








public function addStmt($stmt) {
$stmt = BuilderHelpers::normalizeNode($stmt);

if ($stmt instanceof Stmt\Property) {
$this->properties[] = $stmt;
} elseif ($stmt instanceof Stmt\ClassMethod) {
$this->methods[] = $stmt;
} elseif ($stmt instanceof Stmt\TraitUse) {
$this->uses[] = $stmt;
} elseif ($stmt instanceof Stmt\ClassConst) {
$this->constants[] = $stmt;
} else {
throw new \LogicException(sprintf('Unexpected node of type "%s"', $stmt->getType()));
}

return $this;
}








public function addAttribute($attribute) {
$this->attributeGroups[] = BuilderHelpers::normalizeAttribute($attribute);

return $this;
}






public function getNode(): PhpParser\Node {
return new Stmt\Trait_(
$this->name, [
'stmts' => array_merge($this->uses, $this->constants, $this->properties, $this->methods),
'attrGroups' => $this->attributeGroups,
], $this->attributes
);
}
}
