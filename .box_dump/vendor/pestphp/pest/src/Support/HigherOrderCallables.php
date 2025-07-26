<?php

declare(strict_types=1);

namespace Pest\Support;

use Closure;
use Pest\Expectation;




final readonly class HigherOrderCallables
{



public function __construct(private object $target)
{

}

/**
@template





*/
public function expect(mixed $value): Expectation
{

$value = $value instanceof Closure ? Reflection::bindCallableWithData($value) : $value;

return new Expectation($value);
}

/**
@template





*/
public function and(mixed $value): Expectation
{
return $this->expect($value);
}




public function defer(callable $callable): object
{
Reflection::bindCallableWithData($callable);

return $this->target;
}
}
