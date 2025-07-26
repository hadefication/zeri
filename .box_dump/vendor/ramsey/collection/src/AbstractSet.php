<?php











declare(strict_types=1);

namespace Ramsey\Collection;

/**
@template
@extends




*/
abstract class AbstractSet extends AbstractCollection
{
public function add(mixed $element): bool
{
if ($this->contains($element)) {
return false;
}






parent::offsetSet(null, $element);

return true;
}

public function offsetSet(mixed $offset, mixed $value): void
{
if ($this->contains($value)) {
return;
}

parent::offsetSet($offset, $value);
}
}
