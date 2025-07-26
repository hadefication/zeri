<?php

declare(strict_types=1);

namespace PhpParser\Builder;

use PhpParser;
use PhpParser\BuilderHelpers;
use PhpParser\Modifiers;
use PhpParser\Node;
use PhpParser\Node\Const_;
use PhpParser\Node\Identifier;
use PhpParser\Node\Stmt;

class ClassConst implements PhpParser\Builder {
protected int $flags = 0;

protected array $attributes = [];

protected array $constants = [];


protected array $attributeGroups = [];

protected ?Node $type = null;







public function __construct($name, $value) {
$this->constants = [new Const_($name, BuilderHelpers::normalizeValue($value))];
}









public function addConst($name, $value) {
$this->constants[] = new Const_($name, BuilderHelpers::normalizeValue($value));

return $this;
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






public function makeFinal() {
$this->flags = BuilderHelpers::addModifier($this->flags, Modifiers::FINAL);

return $this;
}








public function setDocComment($docComment) {
$this->attributes = [
'comments' => [BuilderHelpers::normalizeDocComment($docComment)]
];

return $this;
}








public function addAttribute($attribute) {
$this->attributeGroups[] = BuilderHelpers::normalizeAttribute($attribute);

return $this;
}








public function setType($type) {
$this->type = BuilderHelpers::normalizeType($type);

return $this;
}






public function getNode(): PhpParser\Node {
return new Stmt\ClassConst(
$this->constants,
$this->flags,
$this->attributes,
$this->attributeGroups,
$this->type
);
}
}
