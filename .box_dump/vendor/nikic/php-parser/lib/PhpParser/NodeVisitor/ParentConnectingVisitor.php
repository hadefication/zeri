<?php declare(strict_types=1);

namespace PhpParser\NodeVisitor;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

use function array_pop;
use function count;









final class ParentConnectingVisitor extends NodeVisitorAbstract {



private array $stack = [];

private bool $weakReferences;

public function __construct(bool $weakReferences = false) {
$this->weakReferences = $weakReferences;
}

public function beforeTraverse(array $nodes) {
$this->stack = [];
}

public function enterNode(Node $node) {
if (!empty($this->stack)) {
$parent = $this->stack[count($this->stack) - 1];
if ($this->weakReferences) {
$node->setAttribute('weak_parent', \WeakReference::create($parent));
} else {
$node->setAttribute('parent', $parent);
}
}

$this->stack[] = $node;
}

public function leaveNode(Node $node) {
array_pop($this->stack);
}
}
