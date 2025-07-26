<?php

declare(strict_types=1);

namespace PHPUnit\Architecture\Elements;

use Exception;
use Error;
use PhpParser\Node;
use PHPUnit\Architecture\Enums\ObjectType;
use PHPUnit\Architecture\Services\ServiceContainer;
use ReflectionClass;
use ReflectionException;

abstract class ObjectDescriptionBase
{





private static iterable $ignore = [
'Symfony\Component\Console\Tester',
];

public ObjectType $type;

public string $path;




public string $name;




public array $stmts;

public ReflectionClass $reflectionClass; 

public static function make(string $path): ?self
{
$ast = null;
$content = file_get_contents($path);
if ($content === false) {
throw new Exception("Path: '{$path}' not found");
}

try {
$ast = ServiceContainer::$parser->parse($content);
} catch (Exception $e) {

if (ServiceContainer::$showException) {
echo "Path: $path Exception: {$e->getMessage()}";
}
}

if ($ast === null) {
return null;
}

$stmts = ServiceContainer::$nodeTraverser->traverse($ast);


$object = ServiceContainer::$nodeFinder->findFirst($stmts, function (Node $node) {
return $node instanceof Node\Stmt\Class_
|| $node instanceof Node\Stmt\Trait_
|| $node instanceof Node\Stmt\Interface_
|| $node instanceof Node\Stmt\Enum_

;
});

if ($object === null) {
return null;
}

if (!property_exists($object, 'namespacedName')) {
return null;
}

if ($object->namespacedName === null) {
return null;
}

$description = new static(); 

if ($object instanceof Node\Stmt\Class_) {
$description->type = ObjectType::_CLASS;
} elseif ($object instanceof Node\Stmt\Trait_) {
$description->type = ObjectType::_TRAIT;
} elseif ($object instanceof Node\Stmt\Interface_) {
$description->type = ObjectType::_INTERFACE;
} elseif ($object instanceof Node\Stmt\Enum_) {
$description->type = ObjectType::_ENUM;
}


$className = $object->namespacedName->toString();

$description->path = $path;
$description->name = $className;
$description->stmts = $stmts;

foreach (self::$ignore as $ignore) {
if (str_starts_with($className, $ignore)) {
return null;
}
}

try {
$description->reflectionClass = new ReflectionClass($description->name);
} catch (Error|ReflectionException) { 
return null;
}

return $description;
}

public function __toString()
{
return $this->name;
}
}
