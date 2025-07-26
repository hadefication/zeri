<?php

namespace Illuminate\View;

use ArrayIterator;
use Closure;
use Illuminate\Contracts\Support\DeferringDisplayableValue;
use Illuminate\Support\Enumerable;
use IteratorAggregate;
use Stringable;
use Traversable;

class InvokableComponentVariable implements DeferringDisplayableValue, IteratorAggregate, Stringable
{





protected $callable;






public function __construct(Closure $callable)
{
$this->callable = $callable;
}






public function resolveDisplayableValue()
{
return $this->__invoke();
}






public function getIterator(): Traversable
{
$result = $this->__invoke();

return new ArrayIterator($result instanceof Enumerable ? $result->all() : $result);
}







public function __get($key)
{
return $this->__invoke()->{$key};
}








public function __call($method, $parameters)
{
return $this->__invoke()->{$method}(...$parameters);
}






public function __invoke()
{
return call_user_func($this->callable);
}






public function __toString()
{
return (string) $this->__invoke();
}
}
