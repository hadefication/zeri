<?php declare(strict_types=1);

namespace PhpParser;

use PhpParser\NodeVisitor\FindingVisitor;
use PhpParser\NodeVisitor\FirstFindingVisitor;

class NodeFinder {








public function find($nodes, callable $filter): array {
if ($nodes === []) {
return [];
}

if (!is_array($nodes)) {
$nodes = [$nodes];
}

$visitor = new FindingVisitor($filter);

$traverser = new NodeTraverser($visitor);
$traverser->traverse($nodes);

return $visitor->getFoundNodes();
}

/**
@template







*/
public function findInstanceOf($nodes, string $class): array {
return $this->find($nodes, function ($node) use ($class) {
return $node instanceof $class;
});
}









public function findFirst($nodes, callable $filter): ?Node {
if ($nodes === []) {
return null;
}

if (!is_array($nodes)) {
$nodes = [$nodes];
}

$visitor = new FirstFindingVisitor($filter);

$traverser = new NodeTraverser($visitor);
$traverser->traverse($nodes);

return $visitor->getFoundNode();
}

/**
@template







*/
public function findFirstInstanceOf($nodes, string $class): ?Node {
return $this->findFirst($nodes, function ($node) use ($class) {
return $node instanceof $class;
});
}
}
