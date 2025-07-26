<?php










namespace Symfony\Component\EventDispatcher;

use Symfony\Contracts\EventDispatcher\Event;

/**
@implements
@implements






*/
class GenericEvent extends Event implements \ArrayAccess, \IteratorAggregate
{






public function __construct(
protected mixed $subject = null,
protected array $arguments = [],
) {
}




public function getSubject(): mixed
{
return $this->subject;
}






public function getArgument(string $key): mixed
{
if ($this->hasArgument($key)) {
return $this->arguments[$key];
}

throw new \InvalidArgumentException(\sprintf('Argument "%s" not found.', $key));
}






public function setArgument(string $key, mixed $value): static
{
$this->arguments[$key] = $value;

return $this;
}




public function getArguments(): array
{
return $this->arguments;
}






public function setArguments(array $args = []): static
{
$this->arguments = $args;

return $this;
}




public function hasArgument(string $key): bool
{
return \array_key_exists($key, $this->arguments);
}








public function offsetGet(mixed $key): mixed
{
return $this->getArgument($key);
}






public function offsetSet(mixed $key, mixed $value): void
{
$this->setArgument($key, $value);
}






public function offsetUnset(mixed $key): void
{
if ($this->hasArgument($key)) {
unset($this->arguments[$key]);
}
}






public function offsetExists(mixed $key): bool
{
return $this->hasArgument($key);
}






public function getIterator(): \ArrayIterator
{
return new \ArrayIterator($this->arguments);
}
}
