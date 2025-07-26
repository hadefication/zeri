<?php










namespace Symfony\Component\Console\Helper;

use Symfony\Component\Console\Output\OutputInterface;

/**
@implements




*/
final class TreeHelper implements \RecursiveIterator
{



private \Iterator $children;

private function __construct(
private readonly OutputInterface $output,
private readonly TreeNode $node,
private readonly TreeStyle $style,
) {
$this->children = new \IteratorIterator($this->node->getChildren());
$this->children->rewind();
}

public static function createTree(OutputInterface $output, string|TreeNode|null $root = null, iterable $values = [], ?TreeStyle $style = null): self
{
$node = $root instanceof TreeNode ? $root : new TreeNode($root ?? '');

return new self($output, TreeNode::fromValues($values, $node), $style ?? TreeStyle::default());
}

public function current(): TreeNode
{
return $this->children->current();
}

public function key(): int
{
return $this->children->key();
}

public function next(): void
{
$this->children->next();
}

public function rewind(): void
{
$this->children->rewind();
}

public function valid(): bool
{
return $this->children->valid();
}

public function hasChildren(): bool
{
if (null === $current = $this->current()) {
return false;
}

foreach ($current->getChildren() as $child) {
return true;
}

return false;
}

public function getChildren(): \RecursiveIterator
{
return new self($this->output, $this->current(), $this->style);
}




public function render(): void
{
$treeIterator = new \RecursiveTreeIterator($this);

$this->style->applyPrefixes($treeIterator);

$this->output->writeln($this->node->getValue());

$visited = new \SplObjectStorage();
foreach ($treeIterator as $node) {
$currentNode = $node instanceof TreeNode ? $node : $treeIterator->getInnerIterator()->current();
if ($visited->contains($currentNode)) {
throw new \LogicException(\sprintf('Cycle detected at node: "%s".', $currentNode->getValue()));
}
$visited->attach($currentNode);

$this->output->writeln($node);
}
}
}
