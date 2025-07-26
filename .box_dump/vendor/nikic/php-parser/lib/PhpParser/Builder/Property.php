<?php declare(strict_types=1);

namespace PhpParser\Builder;

use PhpParser;
use PhpParser\BuilderHelpers;
use PhpParser\Modifiers;
use PhpParser\Node;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt;
use PhpParser\Node\ComplexType;

class Property implements PhpParser\Builder {
protected string $name;

protected int $flags = 0;

protected ?Node\Expr $default = null;

protected array $attributes = [];

protected ?Node $type = null;

protected array $attributeGroups = [];

protected array $hooks = [];






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






public function makeReadonly() {
$this->flags = BuilderHelpers::addModifier($this->flags, Modifiers::READONLY);

return $this;
}






public function makeAbstract() {
$this->flags = BuilderHelpers::addModifier($this->flags, Modifiers::ABSTRACT);

return $this;
}






public function makeFinal() {
$this->flags = BuilderHelpers::addModifier($this->flags, Modifiers::FINAL);

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








public function setDefault($value) {
$this->default = BuilderHelpers::normalizeValue($value);

return $this;
}








public function setDocComment($docComment) {
$this->attributes = [
'comments' => [BuilderHelpers::normalizeDocComment($docComment)]
];

return $this;
}








public function setType($type) {
$this->type = BuilderHelpers::normalizeType($type);

return $this;
}








public function addAttribute($attribute) {
$this->attributeGroups[] = BuilderHelpers::normalizeAttribute($attribute);

return $this;
}






public function addHook(Node\PropertyHook $hook) {
$this->hooks[] = $hook;

return $this;
}






public function getNode(): PhpParser\Node {
if ($this->flags & Modifiers::ABSTRACT && !$this->hooks) {
throw new PhpParser\Error('Only hooked properties may be declared abstract');
}

return new Stmt\Property(
$this->flags !== 0 ? $this->flags : Modifiers::PUBLIC,
[
new Node\PropertyItem($this->name, $this->default)
],
$this->attributes,
$this->type,
$this->attributeGroups,
$this->hooks
);
}
}
