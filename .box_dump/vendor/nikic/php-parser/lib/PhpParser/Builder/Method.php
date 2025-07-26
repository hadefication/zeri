<?php declare(strict_types=1);

namespace PhpParser\Builder;

use PhpParser;
use PhpParser\BuilderHelpers;
use PhpParser\Modifiers;
use PhpParser\Node;
use PhpParser\Node\Stmt;

class Method extends FunctionLike {
protected string $name;

protected int $flags = 0;


protected ?array $stmts = [];


protected array $attributeGroups = [];






public function __construct(string $name) {
$this->name = $name;
}






public function makePublic() {
$this->flags = BuilderHelpers::addModifier($this->flags, Modifiers::PUBLIC);

return $this;
}






public function makeProtected() {
$this->flags = BuilderHelpers::addModifier($this->flags, Modifiers::PROTECTED);

return $this;
}






public function makePrivate() {
$this->flags = BuilderHelpers::addModifier($this->flags, Modifiers::PRIVATE);

return $this;
}






public function makeStatic() {
$this->flags = BuilderHelpers::addModifier($this->flags, Modifiers::STATIC);

return $this;
}






public function makeAbstract() {
if (!empty($this->stmts)) {
throw new \LogicException('Cannot make method with statements abstract');
}

$this->flags = BuilderHelpers::addModifier($this->flags, Modifiers::ABSTRACT);
$this->stmts = null; 

return $this;
}






public function makeFinal() {
$this->flags = BuilderHelpers::addModifier($this->flags, Modifiers::FINAL);

return $this;
}








public function addStmt($stmt) {
if (null === $this->stmts) {
throw new \LogicException('Cannot add statements to an abstract method');
}

$this->stmts[] = BuilderHelpers::normalizeStmt($stmt);

return $this;
}








public function addAttribute($attribute) {
$this->attributeGroups[] = BuilderHelpers::normalizeAttribute($attribute);

return $this;
}






public function getNode(): Node {
return new Stmt\ClassMethod($this->name, [
'flags' => $this->flags,
'byRef' => $this->returnByRef,
'params' => $this->params,
'returnType' => $this->returnType,
'stmts' => $this->stmts,
'attrGroups' => $this->attributeGroups,
], $this->attributes);
}
}
