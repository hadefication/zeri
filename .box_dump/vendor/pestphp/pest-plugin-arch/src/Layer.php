<?php

declare(strict_types=1);

namespace Pest\Arch;

use IteratorAggregate;
use PHPUnit\Architecture\Elements\Layer\Layer as BaseLayer;
use PHPUnit\Architecture\Elements\ObjectDescription;
use Traversable;

/**
@implements






*/
final class Layer implements IteratorAggregate
{



private function __construct(private readonly BaseLayer $layer)
{

}






public static function fromBase(array $objects): self
{
return new self(new BaseLayer($objects));
}




public function equals(self $layer): bool
{
return $this->layer->equals($layer->layer);
}




public function getIterator(): Traversable
{
return $this->layer->getIterator();
}




public function getBase(): BaseLayer
{
return $this->layer;
}






public function __call(string $name, array $arguments): self
{
return new self($this->layer->{$name}(...$arguments)); 
}
}
