<?php declare(strict_types=1);








namespace PHPUnit\Metadata\Parser;

use function assert;
use function class_exists;
use function method_exists;
use PHPUnit\Metadata\MetadataCollection;

/**
@no-named-arguments


*/
final readonly class ParserChain implements Parser
{
private Parser $attributeReader;
private Parser $annotationReader;

public function __construct(Parser $attributeReader, Parser $annotationReader)
{
$this->attributeReader = $attributeReader;
$this->annotationReader = $annotationReader;
}




public function forClass(string $className): MetadataCollection
{
assert(class_exists($className));

$metadata = $this->attributeReader->forClass($className);

if (!$metadata->isEmpty()) {
return $metadata;
}

return $this->annotationReader->forClass($className);
}





public function forMethod(string $className, string $methodName): MetadataCollection
{
assert(class_exists($className));
assert(method_exists($className, $methodName));

$metadata = $this->attributeReader->forMethod($className, $methodName);

if (!$metadata->isEmpty()) {
return $metadata;
}

return $this->annotationReader->forMethod($className, $methodName);
}





public function forClassAndMethod(string $className, string $methodName): MetadataCollection
{
return $this->forClass($className)->mergeWith(
$this->forMethod($className, $methodName),
);
}
}
