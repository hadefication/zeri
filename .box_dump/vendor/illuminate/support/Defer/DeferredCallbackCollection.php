<?php

namespace Illuminate\Support\Defer;

use ArrayAccess;
use Closure;
use Countable;
use Illuminate\Support\Collection;

class DeferredCallbackCollection implements ArrayAccess, Countable
{





protected array $callbacks = [];






public function first()
{
return array_values($this->callbacks)[0];
}






public function invoke(): void
{
$this->invokeWhen(fn () => true);
}







public function invokeWhen(?Closure $when = null): void
{
$when ??= fn () => true;

$this->forgetDuplicates();

foreach ($this->callbacks as $index => $callback) {
if ($when($callback)) {
rescue($callback);
}

unset($this->callbacks[$index]);
}
}







public function forget(string $name): void
{
$this->callbacks = (new Collection($this->callbacks))
->reject(fn ($callback) => $callback->name === $name)
->values()
->all();
}






protected function forgetDuplicates(): static
{
$this->callbacks = (new Collection($this->callbacks))
->reverse()
->unique(fn ($c) => $c->name)
->reverse()
->values()
->all();

return $this;
}







public function offsetExists(mixed $offset): bool
{
$this->forgetDuplicates();

return isset($this->callbacks[$offset]);
}







public function offsetGet(mixed $offset): mixed
{
$this->forgetDuplicates();

return $this->callbacks[$offset];
}








public function offsetSet(mixed $offset, mixed $value): void
{
if (is_null($offset)) {
$this->callbacks[] = $value;
} else {
$this->callbacks[$offset] = $value;
}
}







public function offsetUnset(mixed $offset): void
{
$this->forgetDuplicates();

unset($this->callbacks[$offset]);
}






public function count(): int
{
$this->forgetDuplicates();

return count($this->callbacks);
}
}
