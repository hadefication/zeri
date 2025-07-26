<?php declare(strict_types=1);

namespace PhpParser\NodeVisitor;

use PhpParser\Node;
use PhpParser\NodeVisitor;
use PhpParser\NodeVisitorAbstract;





class FirstFindingVisitor extends NodeVisitorAbstract {

protected $filterCallback;

protected ?Node $foundNode;

public function __construct(callable $filterCallback) {
$this->filterCallback = $filterCallback;
}








public function getFoundNode(): ?Node {
return $this->foundNode;
}

public function beforeTraverse(array $nodes): ?array {
$this->foundNode = null;

return null;
}

public function enterNode(Node $node) {
$filterCallback = $this->filterCallback;
if ($filterCallback($node)) {
$this->foundNode = $node;
return NodeVisitor::STOP_TRAVERSAL;
}

return null;
}
}
