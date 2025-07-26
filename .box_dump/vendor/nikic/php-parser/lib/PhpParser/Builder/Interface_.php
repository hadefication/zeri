<?php declare(strict_types=1);

namespace PhpParser\Builder;

use PhpParser;
use PhpParser\BuilderHelpers;
use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt;

class Interface_ extends Declaration {
protected string $name;

protected array $extends = [];

protected array $constants = [];

protected array $methods = [];

protected array $attributeGroups = [];






public function __construct(string $name) {
$this->name = $name;
}








public function extend(...$interfaces) {
foreach ($interfaces as $interface) {
$this->extends[] = BuilderHelpers::normalizeName($interface);
}

return $this;
}








public function addStmt($stmt) {
$stmt = BuilderHelpers::normalizeNode($stmt);

if ($stmt instanceof Stmt\ClassConst) {
$this->constants[] = $stmt;
} elseif ($stmt instanceof Stmt\ClassMethod) {

$stmt->stmts = null;
$this->methods[] = $stmt;
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
return new Stmt\Interface_($this->name, [
'extends' => $this->extends,
'stmts' => array_merge($this->constants, $this->methods),
'attrGroups' => $this->attributeGroups,
], $this->attributes);
}
}
