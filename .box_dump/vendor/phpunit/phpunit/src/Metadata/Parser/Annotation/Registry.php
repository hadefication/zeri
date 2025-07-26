<?php declare(strict_types=1);








namespace PHPUnit\Metadata\Annotation\Parser;

use function array_key_exists;
use PHPUnit\Metadata\AnnotationsAreNotSupportedForInternalClassesException;
use PHPUnit\Metadata\ReflectionException;
use ReflectionClass;
use ReflectionMethod;

/**
@no-named-arguments





*/
final class Registry
{
private static ?Registry $instance = null;




private array $classDocBlocks = [];




private array $methodDocBlocks = [];

public static function getInstance(): self
{
return self::$instance ?? self::$instance = new self;
}







public function forClassName(string $class): DocBlock
{
if (array_key_exists($class, $this->classDocBlocks)) {
return $this->classDocBlocks[$class];
}

try {
$reflection = new ReflectionClass($class);


/**
@phpstan-ignore */
} catch (\ReflectionException $e) {
throw new ReflectionException(
$e->getMessage(),
$e->getCode(),
$e,
);
}


return $this->classDocBlocks[$class] = DocBlock::ofClass($reflection);
}







public function forMethod(string $classInHierarchy, string $method): DocBlock
{
if (isset($this->methodDocBlocks[$classInHierarchy][$method])) {
return $this->methodDocBlocks[$classInHierarchy][$method];
}

try {
$reflection = new ReflectionMethod($classInHierarchy, $method);

} catch (\ReflectionException $e) {
throw new ReflectionException(
$e->getMessage(),
$e->getCode(),
$e,
);
}


return $this->methodDocBlocks[$classInHierarchy][$method] = DocBlock::ofMethod($reflection);
}
}
