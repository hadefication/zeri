<?php declare(strict_types=1);








namespace PHPUnit\TextUI\XmlConfiguration;

use function count;
use ArrayIterator;
use Countable;
use DOMNode;
use DOMNodeList;
use IteratorAggregate;

/**
@no-named-arguments
@template-implements



*/
final class SnapshotNodeList implements Countable, IteratorAggregate
{



private array $nodes = [];




public static function fromNodeList(DOMNodeList $list): self
{
$snapshot = new self;

foreach ($list as $node) {
$snapshot->nodes[] = $node;
}

return $snapshot;
}

public function count(): int
{
return count($this->nodes);
}




public function getIterator(): ArrayIterator
{
return new ArrayIterator($this->nodes);
}
}
