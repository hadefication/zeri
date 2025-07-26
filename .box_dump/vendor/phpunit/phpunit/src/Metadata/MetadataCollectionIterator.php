<?php declare(strict_types=1);








namespace PHPUnit\Metadata;

use function count;
use Iterator;

/**
@template-implements
@no-named-arguments

*/
final class MetadataCollectionIterator implements Iterator
{



private readonly array $metadata;
private int $position = 0;

public function __construct(MetadataCollection $metadata)
{
$this->metadata = $metadata->asArray();
}

public function rewind(): void
{
$this->position = 0;
}

public function valid(): bool
{
return $this->position < count($this->metadata);
}

public function key(): int
{
return $this->position;
}

public function current(): Metadata
{
return $this->metadata[$this->position];
}

public function next(): void
{
$this->position++;
}
}
