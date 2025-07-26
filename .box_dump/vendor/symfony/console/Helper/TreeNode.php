<?php










namespace Symfony\Component\Console\Helper;

/**
@implements


*/
final class TreeNode implements \Countable, \IteratorAggregate
{



private array $children = [];

public function __construct(
private readonly string $value = '',
iterable $children = [],
) {
foreach ($children as $child) {
$this->addChild($child);
}
}

public static function fromValues(iterable $nodes, ?self $node = null): self
{
$node ??= new self();
foreach ($nodes as $key => $value) {
if (is_iterable($value)) {
$child = new self($key);
self::fromValues($value, $child);
$node->addChild($child);
} elseif ($value instanceof self) {
$node->addChild($value);
} else {
$node->addChild(new self($value));
}
}

return $node;
}

public function getValue(): string
{
return $this->value;
}

public function addChild(self|string|callable $node): self
{
if (\is_string($node)) {
$node = new self($node, $this);
}

$this->children[] = $node;

return $this;
}




public function getChildren(): \Traversable
{
foreach ($this->children as $child) {
if (\is_callable($child)) {
yield from $child();
} elseif ($child instanceof self) {
yield $child;
}
}
}




public function getIterator(): \Traversable
{
return $this->getChildren();
}

public function count(): int
{
$count = 0;
foreach ($this->getChildren() as $child) {
++$count;
}

return $count;
}

public function __toString(): string
{
return $this->value;
}
}
