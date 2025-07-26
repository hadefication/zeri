<?php declare(strict_types=1);








namespace SebastianBergmann\CodeCoverage\Node;

use function count;
use RecursiveIterator;




final class Iterator implements RecursiveIterator
{
private int $position;




private readonly array $nodes;

public function __construct(Directory $node)
{
$this->nodes = $node->children();
}




public function rewind(): void
{
$this->position = 0;
}




public function valid(): bool
{
return $this->position < count($this->nodes);
}




public function key(): int
{
return $this->position;
}




public function current(): ?AbstractNode
{
return $this->valid() ? $this->nodes[$this->position] : null;
}




public function next(): void
{
$this->position++;
}




public function getChildren(): self
{
return new self($this->nodes[$this->position]);
}




public function hasChildren(): bool
{
return $this->nodes[$this->position] instanceof Directory;
}
}
