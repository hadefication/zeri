<?php declare(strict_types=1);

namespace PhpParser\Builder;

use PhpParser;
use PhpParser\BuilderHelpers;
use PhpParser\Node;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt;

class Enum_ extends Declaration {
protected string $name;
protected ?Identifier $scalarType = null;

protected array $implements = [];

protected array $uses = [];

protected array $enumCases = [];

protected array $constants = [];

protected array $methods = [];

protected array $attributeGroups = [];






public function __construct(string $name) {
$this->name = $name;
}








public function setScalarType($scalarType) {
$this->scalarType = BuilderHelpers::normalizeType($scalarType);

return $this;
}








public function implement(...$interfaces) {
foreach ($interfaces as $interface) {
$this->implements[] = BuilderHelpers::normalizeName($interface);
}

return $this;
}








public function addStmt($stmt) {
$stmt = BuilderHelpers::normalizeNode($stmt);

if ($stmt instanceof Stmt\EnumCase) {
$this->enumCases[] = $stmt;
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
return new Stmt\Enum_($this->name, [
'scalarType' => $this->scalarType,
'implements' => $this->implements,
'stmts' => array_merge($this->uses, $this->enumCases, $this->constants, $this->methods),
'attrGroups' => $this->attributeGroups,
], $this->attributes);
}
}
