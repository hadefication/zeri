<?php declare(strict_types=1);

namespace PhpParser\Builder;

use PhpParser;
use PhpParser\BuilderHelpers;
use PhpParser\Modifiers;
use PhpParser\Node;

class Param implements PhpParser\Builder {
protected string $name;
protected ?Node\Expr $default = null;

protected ?Node $type = null;
protected bool $byRef = false;
protected int $flags = 0;
protected bool $variadic = false;

protected array $attributeGroups = [];






public function __construct(string $name) {
$this->name = $name;
}








public function setDefault($value) {
$this->default = BuilderHelpers::normalizeValue($value);

return $this;
}








public function setType($type) {
$this->type = BuilderHelpers::normalizeType($type);
if ($this->type == 'void') {
throw new \LogicException('Parameter type cannot be void');
}

return $this;
}






public function makeByRef() {
$this->byRef = true;

return $this;
}






public function makeVariadic() {
$this->variadic = true;

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






public function makeReadonly() {
$this->flags = BuilderHelpers::addModifier($this->flags, Modifiers::READONLY);

return $this;
}






public function makePrivateSet() {
$this->flags = BuilderHelpers::addModifier($this->flags, Modifiers::PRIVATE_SET);

return $this;
}






public function makeProtectedSet() {
$this->flags = BuilderHelpers::addModifier($this->flags, Modifiers::PROTECTED_SET);

return $this;
}








public function addAttribute($attribute) {
$this->attributeGroups[] = BuilderHelpers::normalizeAttribute($attribute);

return $this;
}






public function getNode(): Node {
return new Node\Param(
new Node\Expr\Variable($this->name),
$this->default, $this->type, $this->byRef, $this->variadic, [], $this->flags, $this->attributeGroups
);
}
}
