<?php declare(strict_types=1);

namespace PhpParser\Builder;

use PhpParser;
use PhpParser\BuilderHelpers;
use PhpParser\Modifiers;
use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt;

class Class_ extends Declaration {
protected string $name;
protected ?Name $extends = null;

protected array $implements = [];
protected int $flags = 0;

protected array $uses = [];

protected array $constants = [];

protected array $properties = [];

protected array $methods = [];

protected array $attributeGroups = [];






public function __construct(string $name) {
$this->name = $name;
}








public function extend($class) {
$this->extends = BuilderHelpers::normalizeName($class);

return $this;
}








public function implement(...$interfaces) {
foreach ($interfaces as $interface) {
$this->implements[] = BuilderHelpers::normalizeName($interface);
}

return $this;
}






public function makeAbstract() {
$this->flags = BuilderHelpers::addClassModifier($this->flags, Modifiers::ABSTRACT);

return $this;
}






public function makeFinal() {
$this->flags = BuilderHelpers::addClassModifier($this->flags, Modifiers::FINAL);

return $this;
}






public function makeReadonly() {
$this->flags = BuilderHelpers::addClassModifier($this->flags, Modifiers::READONLY);

return $this;
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
return new Stmt\Class_($this->name, [
'flags' => $this->flags,
'extends' => $this->extends,
'implements' => $this->implements,
'stmts' => array_merge($this->uses, $this->constants, $this->properties, $this->methods),
'attrGroups' => $this->attributeGroups,
], $this->attributes);
}
}
