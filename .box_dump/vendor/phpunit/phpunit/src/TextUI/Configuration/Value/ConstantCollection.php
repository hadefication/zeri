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
final readonly class ConstantCollection implements Countable, IteratorAggregate
{



private array $constants;




public static function fromArray(array $constants): self
{
return new self(...$constants);
}

private function __construct(Constant ...$constants)
{
$this->constants = $constants;
}




public function asArray(): array
{
return $this->constants;
}

public function count(): int
{
return count($this->constants);
}

public function getIterator(): ConstantCollectionIterator
{
return new ConstantCollectionIterator($this);
}
}
