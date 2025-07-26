<?php declare(strict_types=1);








namespace SebastianBergmann\Complexity;

use function assert;
use function file_exists;
use function file_get_contents;
use function is_readable;
use function is_string;
use PhpParser\Error;
use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\NodeVisitor\ParentConnectingVisitor;
use PhpParser\ParserFactory;

final class Calculator
{





public function calculateForSourceFile(string $sourceFile): ComplexityCollection
{
assert(file_exists($sourceFile));
assert(is_readable($sourceFile));

$source = file_get_contents($sourceFile);

assert(is_string($source));

return $this->calculateForSourceString($source);
}




public function calculateForSourceString(string $source): ComplexityCollection
{
try {
$nodes = (new ParserFactory)->createForHostVersion()->parse($source);

assert($nodes !== null);

return $this->calculateForAbstractSyntaxTree($nodes);


} catch (Error $error) {
throw new RuntimeException(
$error->getMessage(),
$error->getCode(),
$error,
);
}

}






public function calculateForAbstractSyntaxTree(array $nodes): ComplexityCollection
{
$traverser = new NodeTraverser;
$complexityCalculatingVisitor = new ComplexityCalculatingVisitor(true);

$traverser->addVisitor(new NameResolver);
$traverser->addVisitor(new ParentConnectingVisitor);
$traverser->addVisitor($complexityCalculatingVisitor);

try {

$traverser->traverse($nodes);

} catch (Error $error) {
throw new RuntimeException(
$error->getMessage(),
$error->getCode(),
$error,
);
}


return $complexityCalculatingVisitor->result();
}
}
