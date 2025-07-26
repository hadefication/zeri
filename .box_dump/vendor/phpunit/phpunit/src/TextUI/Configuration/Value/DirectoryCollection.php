<?php declare(strict_types=1);








namespace PHPUnit\TextUI\Configuration;

use function count;
use Countable;
use IteratorAggregate;

/**
@no-named-arguments
@immutable
@template-implements


*/
final readonly class DirectoryCollection implements Countable, IteratorAggregate
{



private array $directories;




public static function fromArray(array $directories): self
{
return new self(...$directories);
}

private function __construct(Directory ...$directories)
{
$this->directories = $directories;
}




public function asArray(): array
{
return $this->directories;
}

public function count(): int
{
return count($this->directories);
}

public function getIterator(): DirectoryCollectionIterator
{
return new DirectoryCollectionIterator($this);
}

public function isEmpty(): bool
{
return $this->count() === 0;
}
}
