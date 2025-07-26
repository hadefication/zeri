<?php declare(strict_types=1);

namespace PhpParser\NodeVisitor;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;












final class NodeConnectingVisitor extends NodeVisitorAbstract {



private array $stack = [];




private $previous;

private bool $weakReferences;

public function __construct(bool $weakReferences = false) {
$this->weakReferences = $weakReferences;
}

public function beforeTraverse(array $nodes) {
$this->stack = [];
$this->previous = null;
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

if ($this->previous !== null) {
if (
$this->weakReferences
) {
if ($this->previous->getAttribute('weak_parent') === $node->getAttribute('weak_parent')) {
$node->setAttribute('weak_previous', \WeakReference::create($this->previous));
$this->previous->setAttribute('weak_next', \WeakReference::create($node));
}
} elseif ($this->previous->getAttribute('parent') === $node->getAttribute('parent')) {
$node->setAttribute('previous', $this->previous);
$this->previous->setAttribute('next', $node);
}
}

$this->stack[] = $node;
}

public function leaveNode(Node $node) {
$this->previous = $node;

array_pop($this->stack);
}
}
