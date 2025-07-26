<?php declare(strict_types=1);

namespace PhpParser\Node\Stmt;

use PhpParser\Node;
use PhpParser\Node\PropertyItem;

abstract class ClassLike extends Node\Stmt {

public ?Node\Identifier $name;

public array $stmts;

public array $attrGroups;


public ?Node\Name $namespacedName;




public function getTraitUses(): array {
$traitUses = [];
foreach ($this->stmts as $stmt) {
if ($stmt instanceof TraitUse) {
$traitUses[] = $stmt;
}
}
return $traitUses;
}




public function getConstants(): array {
$constants = [];
foreach ($this->stmts as $stmt) {
if ($stmt instanceof ClassConst) {
$constants[] = $stmt;
}
}
return $constants;
}




public function getProperties(): array {
$properties = [];
foreach ($this->stmts as $stmt) {
if ($stmt instanceof Property) {
$properties[] = $stmt;
}
}
return $properties;
}








public function getProperty(string $name): ?Property {
foreach ($this->stmts as $stmt) {
if ($stmt instanceof Property) {
foreach ($stmt->props as $prop) {
if ($prop instanceof PropertyItem && $name === $prop->name->toString()) {
return $stmt;
}
}
}
}
return null;
}






public function getMethods(): array {
$methods = [];
foreach ($this->stmts as $stmt) {
if ($stmt instanceof ClassMethod) {
$methods[] = $stmt;
}
}
return $methods;
}








public function getMethod(string $name): ?ClassMethod {
$lowerName = strtolower($name);
foreach ($this->stmts as $stmt) {
if ($stmt instanceof ClassMethod && $lowerName === $stmt->name->toLowerString()) {
return $stmt;
}
}
return null;
}
}
