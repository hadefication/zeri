<?php declare(strict_types=1);

namespace PhpParser\Builder;

use PhpParser;
use PhpParser\BuilderHelpers;
use PhpParser\Node;
use PhpParser\Node\Stmt;

class Function_ extends FunctionLike {
protected string $name;

protected array $stmts = [];


protected array $attributeGroups = [];






public function __construct(string $name) {
$this->name = $name;
}








public function addStmt($stmt) {
$this->stmts[] = BuilderHelpers::normalizeStmt($stmt);

return $this;
}








public function addAttribute($attribute) {
$this->attributeGroups[] = BuilderHelpers::normalizeAttribute($attribute);

return $this;
}






public function getNode(): Node {
return new Stmt\Function_($this->name, [
'byRef' => $this->returnByRef,
'params' => $this->params,
'returnType' => $this->returnType,
'stmts' => $this->stmts,
'attrGroups' => $this->attributeGroups,
], $this->attributes);
}
}
