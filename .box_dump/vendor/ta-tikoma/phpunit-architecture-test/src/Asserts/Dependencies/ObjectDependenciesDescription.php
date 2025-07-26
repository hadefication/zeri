<?php

declare(strict_types=1);

namespace PHPUnit\Architecture\Asserts\Dependencies;

use Throwable;
use PhpParser\Node;
use PHPUnit\Architecture\Asserts\Dependencies\Elements\ObjectUses;
use PHPUnit\Architecture\Elements\ObjectDescriptionBase;
use PHPUnit\Architecture\Services\ServiceContainer;
use phpDocumentor\Reflection\Type;
use phpDocumentor\Reflection\Types\AggregatedType;
use phpDocumentor\Reflection\Types\Array_;
use PHPUnit\Architecture\Elements\ObjectDescription;




abstract class ObjectDependenciesDescription extends ObjectDescriptionBase
{
public ObjectUses $uses;

public static function make(string $path): ?self
{

$description = parent::make($path);
if ($description === null) {
return null;
}


$names = ServiceContainer::$nodeFinder->findInstanceOf(
$description->stmts,
Node\Name::class
);

$names = array_values(array_filter($names, static function (Node\Name $name) {
$nameAsString = $name->toString();

try {
return match (true) {
function_exists($nameAsString) => true,
enum_exists($nameAsString) => true,
class_exists($nameAsString) => true,
interface_exists($nameAsString) => true,
trait_exists($nameAsString) => true,

default => false,
};
} catch (Throwable) {
return false;
}
}));

$description->uses = new ObjectUses(
array_map(
static function (Node\Name $nodeName): string {
$name = $nodeName->toCodeString();
if ($name[0] !== '\\') {
return $name;
}
return substr($name, 1);
},
$names
)
);

return $description;
}




public function getDocBlockTypeWithNamespace(
Type $type
): string|array|null {
$result = [];
if ($type instanceof AggregatedType) {
foreach ($type as $_type) {

$t = $this->getDocBlockTypeWithNamespace($_type);
if ($t !== null) {
$result[] = $t;
}
}
}

if ($type instanceof Array_) {






}


if (count($result) !== 0) {
$_result = [];
foreach ($result as $item) {
if (is_array($item)) {
$_result = array_merge($_result, $item);
} else {
$_result[] = $item;
}
}

return $_result;
}

return $this->uses->getByName((string) $type) ?? (string) $type;
}
}
